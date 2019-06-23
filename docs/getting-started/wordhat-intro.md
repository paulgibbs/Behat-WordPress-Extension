description: How to get started with WordHat
# WordHat

When you start a new project with Behat, perhaps the first things to think about -- after integrating <abbr title="Behavior Driven Development">BDD</abbr> into your workflow -- will be getting browser automation working, and writing your first tests.

Otherwise, if you are unsure how to proceed, first check the [FAQ](/recipes/faq.md) and the other recipe pages in the menu. These will be added to when we receive repeat questions. If you encounter a bug, have a suggestion, or would like to help the project grow, please get in contact via [Github](https://github.com/paulgibbs/behat-wordpress-extension) or our [Slack](https://wordhat.herokuapp.com).


## Browser automation

One of the fun things about using Behat is seeing it control your web browser and navigating your site. Behat relies on a library called [Mink](http://mink.behat.org/en/latest/) to interface with your web browser. Mink has its own driver system, with [each driver supporting a different combination of browser features](http://mink.behat.org/en/latest/guides/drivers.html#driver-feature-support).

Some Mink drivers run headless web browsers (e.g. Goutte), and these tend to be the fastest, though notably, most do not support Javascript. WordHat's sample configuration file is configured to use Google Chrome by default, which supports a broad range of browser features for most use cases.

For tests requiring Javascript interactions, mark the scenario or feature with the `@javascript` [tag](http://behat.org/en/latest/user_guide/organizing.html#tags).

## Your first tests

To confirm your Behat set up is working, copy any one (or all!) of WordHat's own tests from `vendor/paulgibbs/behat-wordpress-extension/features/*.feature` into your project's `features/` folder.

Run Behat with `vendor/bin/behat`, and the tests you copied should pass if everything is working.
