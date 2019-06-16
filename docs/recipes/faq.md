description: WordHat frequently asked questions and answers
# FAQs

If you are new to the project or Behat, we recommend that you first [read through our documentation](https://wordhat.info/). For any questions, feedback, or to contribute, you can get in contact with us via Github or our [Slack](https://wordhat.herokuapp.com).

## Browsers
* If you are using [Selenium](http://docs.seleniumhq.org/download/) to run Javascript tests, and you access your WordPress site over HTTPS, *and* your site has a self-signed certificate, you will need to manually configure the web browser to accept that certificate.

## Drivers
* If you are using the WP-CLI driver to [connect to a remote WordPress site over SSH](https://make.wordpress.org/cli/handbook/running-commands-remotely/), WordHat assumes the remote server is Linux-like, with a shell that provides [GNU Coreutils](https://www.gnu.org/software/coreutils/coreutils.html).
* To configure WordHat to use a specific driver, set [`default_driver`](/configuration/settings.md) in your `behat.yml`.
* As of v1.1.0, it is possible to use multiple drivers at the same time. This should be considered an experimental feature for advanced users only. To enable it, tag a scenario with the name of the driver to use (e.g. `@wpcli` or `@wpphp`).

## Selenium
* On a Mac, Selenium is incompatible with the default Apple Java; error messages look like "Unsupported major.minor version 52.0". To fix, install [Oracle Java Platform JDK](http://www.oracle.com/technetwork/java/javase/downloads/index.html).
* With Selenium, some environments may require a "webdriver"; error messages may refer to "GeckoWebDriver" or "ChromeWebDriver", etc. Good solutions include [vvo/selenium-standalone](https://github.com/vvo/selenium-standalone#command-line-interface) or [joomla-projects/selenium-server-standalone](https://github.com/joomla-projects/selenium-server-standalone).

## Supported platforms
* WordHat supports MacOS, most flavours of Linux, and Windows. We use [Travis-CI](https://travis-ci.org/paulgibbs/behat-wordpress-extension) to test on Ubuntu, and require modern versions of PHP.

## Virtual machines (Vagrant)
* If you are using a Vagrant-powered virtual machine to run Behat via the [WP-CLI driver](../features/overview.html#wp-cli), you will need to `vagrant ssh` into the box once. This will set up password-less authentication; otherwise, WordHat will prompt you for the SSH password frequently.

## WordPress
* If your WordPress is installed in a subdirectory, you need to set the `site_url` option to the value of the "WordPress address (URL)" option (found in WordPress > Settings > General). For more information, [consult the WordHat documentation](/configuration/settings.html).
