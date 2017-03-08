# Contexts

With Behat, it is possible to flexibly structure your code by using multiple [contexts](http://docs.behat.org/en/latest/user_guide/context.html) in a single test suite. WordHat provides the following contexts:


## RawWordpressContext

This is a context that provides no step definitions, but all of the necessary functionality for interacting with WordPress and the browser.


## ContentContext

Provides step definitions for creating content: post types, comments, and terms.


## DashboardContext

Provides step definitions that are specific to the WordPress dashboard (wp-admin).


## SiteContext

Provides step definitions for managing plugins, themes, and cache.


## UserContext

Provides step definitions for all things relating to users.


## WordpressContext

Provides step definitions for a range of common tasks. Recommended for all test suites.


## DebugContext

Provides step definitions for debugging step definitions. Not recommended for use in production.
