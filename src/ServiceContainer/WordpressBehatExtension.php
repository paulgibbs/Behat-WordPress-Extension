<?php
namespace PaulGibbs\WordpressBehatExtension\ServiceContainer;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Behat\Testwork\ServiceContainer\ServiceProcessor;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\FileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

use PaulGibbs\WordpressBehatExtension\Compiler\DriverPass;
use PaulGibbs\WordpressBehatExtension\Compiler\DriverElementPass;

use RuntimeException;

/**
 * Main part of the Behat extension.
 */
class WordpressBehatExtension implements ExtensionInterface
{
    /**
     * @var ServiceProcessor
     */
    protected $processor;

    /**
     * Constructor.
     *
     * @param ServiceProcessor|null $processor Optional.
     */
    public function __construct(ServiceProcessor $processor = null)
    {
        $this->processor = $processor ?: new ServiceProcessor();
    }

    /**
     * Returns the extension config key.
     *
     * @return string
     */
    public function getConfigKey()
    {
        return 'wordpress';
    }

    /**
     * Initialise extension.
     *
     * This method is called immediately after all extensions are activated but
     * before any extension `configure()` method is called. This allows extensions
     * to hook into the configuration of other extensions providing such an
     * extension point.
     *
     * @param ExtensionManager $extension_manager
     */
    public function initialize(ExtensionManager $extension_manager)
    {
        $extension_manager->activateExtension('SensioLabs\Behat\PageObjectExtension');
    }

    /**
     * Declare configuration options for the extension.
     *
     * @param ArrayNodeDefinition $builder
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                // Common settings.
                ->scalarNode('default_driver')->defaultValue('wpcli')->end()
                ->scalarNode('custom_driver')->end()
                ->scalarNode('path')->end()

                // WordPress' "siteurl" option.
                ->scalarNode('site_url')->defaultValue('%mink.base_url%')->end()

                // Account roles -> username/password.
                ->arrayNode('users')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('admin')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('username')
                                    ->defaultValue('admin')
                                ->end()
                                ->scalarNode('password')
                                    ->defaultValue('admin')
                                ->end()
                            ->end()
                        ->end()

                        ->arrayNode('editor')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('username')
                                    ->defaultValue('editor')
                                ->end()
                                ->scalarNode('password')
                                    ->defaultValue('editor')
                                ->end()
                            ->end()
                        ->end()

                        ->arrayNode('author')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('username')
                                    ->defaultValue('author')
                                ->end()
                                ->scalarNode('password')
                                    ->defaultValue('author')
                                ->end()
                            ->end()
                        ->end()

                        ->arrayNode('contributor')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('username')
                                    ->defaultValue('contributor')
                                ->end()
                                ->scalarNode('password')
                                    ->defaultValue('contributor')
                                ->end()
                            ->end()
                        ->end()

                        ->arrayNode('subscriber')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('username')
                                    ->defaultValue('subscriber')
                                ->end()
                                ->scalarNode('password')
                                    ->defaultValue('subscriber')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                // WP-CLI driver.
                ->arrayNode('wpcli')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('alias')->end()
                        ->scalarNode('binary')->end()
                    ->end()
                ->end()

                // WordPress API driver.
                ->arrayNode('wpapi')
                    ->addDefaultsIfNotSet()
                    ->children()
                    ->end()
                ->end()

                // Blackbox driver.
                ->arrayNode('blackbox')
                    ->addDefaultsIfNotSet()
                    ->children()
                    ->end()
                ->end()

                // Permalink patterns.
                ->arrayNode('permalinks')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('author_archive')
                            ->defaultValue('author/%s/')
                        ->end()
                    ->end()
                ->end()

            ->end()
        ->end();
    }

    /**
     * Load extension services into ServiceContainer.
     *
     * @param ContainerBuilder $container
     * @param array            $config
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/config'));
        $loader->load('services.yml');

        if (isset($config['custom_driver'])) {
            $file = $container->getParameterBag()->resolveValue($config['custom_driver']);
            $loader->load($file);
        }

        $container->setParameter('wordpress.wordpress.default_driver', $config['default_driver']);
        $container->setParameter('wordpress.path', $config['path']);
        $container->setParameter('wordpress.parameters', $config);

        $this->setupWpcliDriver($loader, $container, $config);
        $this->setupWpapiDriver($loader, $container, $config);
        $this->setupBlackboxDriver($loader, $container, $config);
    }

    /**
     * Load settings for the WP-CLI driver.
     *
     * @param FileLoader       $loader
     * @param ContainerBuilder $container
     * @param array            $config
     */
    protected function setupWpcliDriver(FileLoader $loader, ContainerBuilder $container, $config)
    {
        if (! isset($config['wpcli'])) {
            return;
        }

        $loader->load('drivers/wpcli.yml');

        if (empty($config['wpcli']['alias']) && empty($config['path'])) {
            throw new RuntimeException('WP-CLI driver requires an `alias` or root `path` set.');
        }

        $config['wpcli']['alias'] = isset($config['wpcli']['alias']) ? $config['wpcli']['alias'] : '';
        $container->setParameter('wordpress.driver.wpcli.alias', $config['wpcli']['alias']);

        $config['wpcli']['path'] = isset($config['path']) ? $config['path'] : '';
        $container->setParameter('wordpress.driver.wpcli.path', $config['path']);

        $config['wpcli']['binary'] = isset($config['wpcli']['binary']) ? $config['wpcli']['binary'] : null;
        $container->setParameter('wordpress.driver.wpcli.binary', $config['wpcli']['binary']);
    }

    /**
     * Load settings for the WordPress API driver.
     *
     * @param FileLoader       $loader
     * @param ContainerBuilder $container
     * @param array            $config
     */
    protected function setupWpapiDriver(FileLoader $loader, ContainerBuilder $container, $config)
    {
        if (! isset($config['wpapi'])) {
            return;
        }

        $loader->load('drivers/wpapi.yml');

        if (empty($config['path'])) {
            throw new RuntimeException('WordPress API driver requires a root `path` set.');
        }

        $config['wpapi']['path'] = isset($config['path']) ? $config['path'] : '';
        $container->setParameter('wordpress.driver.wpapi.path', $config['wpapi']['path']);
    }

    /**
     * Load settings for the blackbox driver.
     *
     * @param FileLoader       $loader
     * @param ContainerBuilder $container
     * @param array            $config
     */
    protected function setupBlackboxDriver(FileLoader $loader, ContainerBuilder $container, $config)
    {
        if (! isset($config['blackbox'])) {
            return;
        }

        $loader->load('drivers/blackbox.yml');
    }

    /**
     * Modify the container before Symfony compiles it.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->processDriverPass($container);
        $this->processDriverElementPass($container);
        $this->processClassGenerator($container);
        $this->setPageObjectNamespaces($container);
        $this->injectSiteUrlIntoPageObjects($container);
    }

    /**
     * Set up driver registration.
     *
     * @param ContainerBuilder $container
     */
    protected function processDriverPass(ContainerBuilder $container)
    {
        $driver = new DriverPass();
        $driver->process($container);
    }

    /**
     * Set up driver extension registration.
     *
     * @param ContainerBuilder $container
     */
    protected function processDriverElementPass(ContainerBuilder $container)
    {
        $driver = new DriverElementPass();
        $driver->process($container);
    }

    /**
     * Set up custom Context class.
     *
     * `behat --init` creates an inital Context class. Here, we switch the template used for that.
     */
    protected function processClassGenerator(ContainerBuilder $container)
    {
        $definition = new Definition('PaulGibbs\WordpressBehatExtension\Context\ContextClass\ClassGenerator');
        $container->setDefinition(ContextExtension::CLASS_GENERATOR_TAG . '.simple', $definition);
    }

    /**
     * Tell Page Object Extension the namepsace of our page objects
     *
     * @param ContainerBuilder $container
     */
    protected function setPageObjectNamespaces(ContainerBuilder $container)
    {
        // Append our namespaces as earlier namespaces take precedence.
        $pages = $container->getParameter('sensio_labs.page_object_extension.namespaces.page');
        $pages[] = 'PaulGibbs\WordpressBehatExtension\PageObject';

        $elements = $container->getParameter('sensio_labs.page_object_extension.namespaces.element');
        $elements[] = 'PaulGibbs\WordpressBehatExtension\PageObject\Element';

        $container->setParameter('sensio_labs.page_object_extension.namespaces.page', $pages);
        $container->setParameter('sensio_labs.page_object_extension.namespaces.element', $elements);
    }

    /**
     * Adds the WordPress site url as a page parameter into page objects
     */
    protected function injectSiteUrlIntoPageObjects(ContainerBuilder $container)
    {
        $page_parameters = $container->getParameter('sensio_labs.page_object_extension.page_factory.page_parameters');
        $page_parameters = $container->getParameterBag()->resolveValue($page_parameters);
        $page_parameters['site_url'] = $container->getParameter('wordpress.parameters')['site_url'];
        $container->setParameter('sensio_labs.page_object_extension.page_factory.page_parameters', $page_parameters);
    }
}
