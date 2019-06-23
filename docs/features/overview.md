description: Behat Feature files for WordPress
# Overview

WordHat provides WordPress-specific functionality for common testing scenarios that are specific to WordPress sites. We do this by providing a range of [Behat contexts](/getting-started/behat-intro.md#contexts) that provide useful step definitions.

For convenience, our `behat.yml.dist` configuration template loads all of our contexts by default, though Behat does support [more complex configuration](http://behat.org/en/latest/user_guide/configuration/suites.html) for advanced use cases.

To find out which step definitions are available for your tests, run `vendor/bin/behat --definitions i` in your terminal.


## Drivers

The `Given` and `When` steps in a [Behat Scenario](/getting-started/behat-intro.md#scenarios) configure a WordPress into a known state for reliable testing. WordHat abstracts this communication between WordPress and itself into a *drivers* system. Two drivers are currently provided: WP-CLI (the default), and WP-PHP.

!!! info "Drivers"
    WordHat only uses a driver to configure a WordPress into a known state.

    Your actual tests are run in a web browser, via [Mink](http://mink.behat.org/en/latest/).

To configure WordHat to use a specific driver, set [`default_driver`](/configuration/settings.md) in your `behat.yml`.

### WP-CLI

The WP-CLI driver uses [WP-CLI](https://wp-cli.org) to connect to WordPress.
This is the default and recommended driver, and allows you to [run your tests and your WordPress site on different servers](https://make.wordpress.org/cli/handbook/running-commands-remotely/).

### WP-PHP

The WP-PHP driver loads WordPress by bootstrapping it directly. This approach was taken from WordPress' [PHPUnit integration test framework](https://make.wordpress.org/core/handbook/testing/automated-testing/phpunit/). You have to run your tests directly on your WordPress server.


## Contexts

### Content

The `ContentContext` context provides a range of step definitions for managing post types, taxonomy terms, and comments.

### Debug

The `DebugContext` context provides step definitions to help debug Scenarios during development.

### Site

The `SiteContext` context provides step definitions for activating/deactivating plugins, switching themes, and clearing the object cache.

### TinyMCE

The `EditPost` context provides step definitions to manage editing and creating content within wp-admin. At time of writing, no support is yet provided for Gutenberg.

### Toolbar

The `ToolbarContext` context provides step definitions to interact with the WordPress Toolbar.

### User

The `UserContext` context provides a range of step definitions for managing users.

### Widget

The `WidgetContext` context provides step definitions to add/remove widgets from sidebars.

### wp-admin

The `Dashboard` context provides a range of step definitions for generally interacting with, and navigating around, the WordPress administration screens (i.e. wp-admin).
