description: How to get install Behat, WordPress, and WordHat
# Installing WordHat

## Requirements

WordHat requires [PHP](https://php.net/) (version 7.1+), [Composer](https://getcomposer.org/), and a [WordPress](https://wordpress.org/) site to test (version 4.8+).

We strongly recommend using [WP-CLI](https://wp-cli.org/)[^1] \(version 1.3.0+), and [Selenium](http://www.seleniumhq.org/)[^2] to help with browser automation. Selenium is fiddly to set up, so we recommend using [vvo/selenium-standalone](https://github.com/vvo/selenium-standalone#command-line-interface), which requires the [Java Platform JDK](http://www.oracle.com/technetwork/java/javase/downloads/index.html).


## Installation

<ol>

<li>Create a folder for your tests. From a terminal:
    <pre><code>mkdir project
cd project</code></pre>
</li>

<li>Tell <a href="https://getcomposer.org/">Composer</a> to download WordHat:
    <pre><code>composer require --dev paulgibbs/behat-wordpress-extension behat/mink-goutte-driver behat/mink-selenium2-driver</code></pre>
</li>

<li>Copy WordHat's sample configuration file into your <code>project</code> folder and rename it:
    <pre><code>cp vendor/paulgibbs/behat-wordpress-extension/behat.yml.dist behat.yml</code></pre>
</li>

<li><p>Edit your <code>behat.yml</code> and: <ul style="list-style-type: lower-alpha"><li>Update the <code>base_url</code> setting with the URL of the website that you intend to test.</li>
<li>Update the <code>path</code> setting with either the relative or absolute path to your WordPress' files.</li>
<li>Update the <code>users</code> section, and specify a username and password of an administrator user account in your WordPress.</li>
<li>If your WordPress is installed in a subdirectory, <a href="/recipes/faq/index.html#wordpress">consult the FAQ for information about the <code>site_url</code> setting</a>.</li></ul>
</li>

<li>Initialise <a href="http://behat.org">Behat</a>:
    <pre><code>vendor/bin/behat --init</code></pre>

    <div class="admonition info">
        <p class="admonition-title">What does this do?</p>
        <p>This creates a <code>features/</code> folder for your <a href="http://docs.behat.org/en/latest/user_guide/features_scenarios.html#features">Features (tests)</a>, and a new <a href="http://docs.behat.org/en/latest/user_guide/context.html">Context class</a>. These will come in handy later!</p>
    </div>
</li>

<li>To confirm that everything is set up correctly, run:
    <pre><code>vendor/bin/behat -dl</code></pre>
    If it worked, you will see a list of text that looks a little like the following (but much longer):
    <pre><code>Given I am an anonymous user
Given I am not logged in
Given I am logged in as a user with the :role role(s)
Given I am logged in as :name
&hellip;</pre></code>
</li>

</ol>


## Next steps

Now that you have WordHat set up, we recommend reading our [introduction to Behat](behat-intro.md) to help you learn the basics before you start writing tests for your site.


[^1]:
    The WP-CLI executable *must* be named `wp` and be within your system's <a href="https://en.wikipedia.org/wiki/PATH_(variable)" id="WP-CLI">$PATH</a>.

[^2]:
    Selenium is recommended for testing <a href="http://mink.behat.org/en/latest/guides/drivers.html" id="SEL">websites that require Javascript</a>. Behat requires the [Mink Selenium2 library](https://packagist.org/packages/behat/mink-selenium2-driver), which we include in the installation instructions above.
