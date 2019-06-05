description: WordHat project news and changelog
# News

WordHat does not yet support WordPress' new content editor (Gutenberg). The step definitions for creating content assume, and only work with, the classic WordPress editor.

## [3.2.0] - 2019-06-05
### Added
- CI: Run build checks on Travis-CI.

### Changed
- Composer: update dependencies to latest constrained versions (#244).
- Composer, Dev Packages: switch `wp-cli` package to `wp-cli-bundle`.
- Mink: restart browser between scenarios instead of resetting (#245).
- Contexts, User: tick "remember me" when a user logs-in to WordPress (#245).

### Fixed
- Update WordHat's internal tests for compatibility with recent versions of WordPress!
- Contexts, User: attempts made to improve intermittent failures with the log-in action (#47, #245).
- Drivers, WP-CLI: allow use of `page_template` when creating content (#239).

## [3.1.2] - 2019-04-02
### Fixed
- Contexts, User: fix errors and exceptions caused by trying to log-out before a browser is open.

## [3.1.1] - 2019-02-27
### Changed
- Travis-CI/Appveyor automated checks have been temporarily removed from the project.

### Fixed
- Contexts, User: attempts made to improve intermittent failures with the log-in action (#47).

## [3.1.0] - 2018-12-28
### Changed
- Contexts, User: the "given there are users..." step definition now auto-sets the new user's password (#228).
- Drivers: Deprecate `validateCredentials` in `UserElement` classes; add no-op. Methods will be removed in 4.0.0 (#220).
- Remove lazy assumption that a tested site has jQuery (#213).

### Fixed
- Website: improve kerning in project logo.
- Features: fix typo in `feature/theme.feature`.
- Drivers: fix finding content by `post_title` when sticky posts exist (#218).

## [3.0.0] - 2018-08-09
### Changed
- Contexts, User: change "I am logged in as ..." step definition to disambiguate between role names and user names. e.g. "Given I am logged in as USER" vs "Given I am logged in as an ROLE".
- Change `users` settings' format in `behat.yml`. [Refer to website](/configuration/settings.md) and/or `behat.yml.dist`.
- Github: update issue templates.
- Website: complete documentation of PHP exception error codes, and misc. tidy-up.
- Website: update mkDocs configuration for its latest version.

## Removed
- Remove deprecated `SnippetAcceptingContext` context from Behat context generator. Use `vendor/bin/behat --snippets-for=MyContext --append-snippets --dry-run` instead.

## [2.0.0] - 2018-06-03
### Added
- Website: add "Open Source" page, and misc. SEO improvements.
- Drivers, WP-CLI: in `CommentElement::create()`, add support for `comment_approved`, `comment_karma`, and `comment_type` arguments.

### Changed
- WordHat now requires PHP 7.1+.
- Build tooling and continuous integration reliability improvements.

### Fixed
- Fix null session when first step asserts a log-in.

## [1.2.0] - 2018-03-20
### Added
- [All PHP exceptions now include an error code](/recipes/errors/overview.md).

### Changed
- WP-CLI driver is more reliable at showing error messages from WP-CLI when something goes wrong.

## [1.1.0] - 2018-03-05
### Added
- Support for running multiple drivers simultaneously.
- Minor PHPDoc adjustments.
- Minor website updates (primary navigation changes, and robots.txt).

### Changed
- Require WP-CLI v1.5+.
- Most of the internals now use strict typehinting.
- Drivers: the "path" argument is now optional.
- Composer dependencies updated to more recent versions.

### Fixed
- Step `iAmViewingAuthorArchive` now correctly accepts a user role as its parameter.

## [1.0.0] - 2018-01-18
### Added
- New step definitions (8 new, 7 changed, and 1 removed).
- Full test coverage for all step definitions and internal WordPress drivers.
- Improved documentation.

### Changed
- Now requires PHP 7+.
- Internal API adjustments: Contexts now only map step definitions to code, functions used by steps have been moved into traits, and drivers encapsulate <abbr title="Create, Retrieve, Update, Delete">CRUD</abbr> operations).
- PHPDoc improvements.
- Use [PHPStan](https://github.com/phpstan/phpstan) for static analysis instead of Scrutinizer-CI.
- New Docker environment for Travis CI; increases build confidence and reliability.

### Fixed
- Many issues discovered by completing our own test coverage.
- AppVeyor fixes.

## [0.9.2] - 2017-12-18
### Fixed
- Fix (more) invalid YAML.

## [0.9.1] - 2017-12-14
### Fixed
- Fix invalid YAML in services.yml.

## [0.9.0] - 2017-10-09
### Changed
- PHPDoc updated/tweaked.
- Rename some `PageObjects` classes for internal consistency.

### Fixed
- Fix return value when creating users.
- Fix creating content with terms or meta, with WP-CLI driver.

### Removed
- Removed unused internal function `isWordpressError()`.
- Removed Composer package `roave/security-advisories`.
- Removed unused Context class.

## [0.8.0] - 2017-09-18
### Added
- Finished implementing database content rollback options; consult documentation.

### Changed
- WordHat now requires PHP 7.0+.
- Renamed "WP-API" driver to "WP-PHP" (backwards-compatible, as long as you have not extended driver classes).
- Website; significant documentation update, especially for on-boarding new users.
- `Path` setting can now be a relative or absolute path. Previously, only absolute paths were supported.

### Fixed
- Website; mkdocs-material theme update to latest version (accessibility fixes).
- Various PHPDoc improvements and corrections.

## [0.7.1] - 2017-08-21
### Changed
- `Path` setting can now be a relative or absolute path. Previously, only absolute paths were supported.

## [0.7.0] - 2017-06-30
### Added
- `ContentEditor` and `PostContentEditor` page (element) objects for interacting with TinyMCE elements.
- `EditPostContext`.
- `WidgetContext`.
- Add optional `redirect_to` param to `logIn()`.

### Changed
- Driver interface re-organisation. See #21.

### Fixed
- If a browser window is not open when the `BeforeStep` event is run, then our call to Selenium2Driver::executeScript() will throw an exception.
- Compatibility with WordPress 4.8.
- Toolbar page object: fix calls to `evaluateScript`.
- Strict version requirement for WP-CLI.
- Links and buttons behind the WordPress' Toolbar cannot be interacted with.

### Deprecated
- Rename `is_wordpress_error()` to `isWordPressError()`.

## [0.6.0] - 2017-04-05
### Added
- Initial support for [sensiolabs/behat-page-object-extension](https://github.com/sensiolabs/BehatPageObjectExtension) with support for parts of wp-admin, and the Toolbar.

### Changed
- Documentation corrections; website and PHPDoc.
- WP-CLI driver no longer fails if the command returns text through stdout.
- Travis-CI reports job status to our Slack; join us at https://wordhat.herokuapp.com 😀
- Composer requirements loosened for better compatibility with other projects.

### Fixed
- Travis-CI tasks now succesfully run on Github forks of the project.
- Attempts made to improve intermittent failures with the log-in action when run with Selenium. Work-in-progress.
- Regex correction for `given` block for `ContentContext->thereArePosts()`.

### Deprecated
- `isWordpressError()` moved into `Util` namespace.

## [0.5.0] - 2017-02-08
### Added
- PHPCS rules.
- Scrutinizer-CI integration.

### Changed
- Website; switched from Couscous to MkDocs.
- Documentation.
- Travis-CI tweaks.

### Fixed
- Miscellanous driver fixes, especially WP-CLI over SSH. Again.

## [0.4.0] - 2017-01-30
### Added
- Introduce [sensiolabs/behat-page-object-extension](https://github.com/sensiolabs/BehatPageObjectExtension) for future development.

### Fixed
- Miscellanous driver fixes, especially WP-CLI over SSH.

### Changed
- Documentation.
- Website design and performance improvements.
- Travis-CI improvements.

## [0.3.0] - 2017-01-07
### Added
- Miscellanous driver fixes.
- First pass at Contexts.

### Changed
- Documentation.

## [0.2.0] - 2016-11-26
### Added
- WP-API and blackbox drivers.
- Website/documentation.
- Database import/export methods to drivers.

### Changed
- Adjusted exceptions thrown by DriverManager and Drivers.
- Design adjustments to website.

### Fixed
- Miscellanous WP-CLI driver fixes.

## [0.1.0] - 2016-09-22
### Added
- First working version of basic architecture.

[3.2.0]: https://github.com/paulgibbs/behat-wordpress-extension/compare/v3.1.2...v3.2.0
[3.1.2]: https://github.com/paulgibbs/behat-wordpress-extension/compare/v3.1.1...v3.1.2
[3.1.1]: https://github.com/paulgibbs/behat-wordpress-extension/compare/v3.1.0...v3.1.1
[3.1.0]: https://github.com/paulgibbs/behat-wordpress-extension/compare/v3.0.0...v3.1.0
[3.0.0]: https://github.com/paulgibbs/behat-wordpress-extension/compare/v2.0.0...v3.0.0
[2.0.0]: https://github.com/paulgibbs/behat-wordpress-extension/compare/v1.2.0...v2.0.0
[1.2.0]: https://github.com/paulgibbs/behat-wordpress-extension/compare/v1.1.0...v1.2.0
[1.1.0]: https://github.com/paulgibbs/behat-wordpress-extension/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/paulgibbs/behat-wordpress-extension/compare/v0.9.0...v1.0.0
[0.9.2]: https://github.com/paulgibbs/behat-wordpress-extension/compare/v0.9.1...v0.9.2
[0.9.1]: https://github.com/paulgibbs/behat-wordpress-extension/compare/v0.9.0...v0.9.1
[0.9.0]: https://github.com/paulgibbs/behat-wordpress-extension/compare/v0.8.0...v0.9.0
[0.8.0]: https://github.com/paulgibbs/behat-wordpress-extension/compare/v0.7.0...v0.8.0
[0.7.1]: https://github.com/paulgibbs/behat-wordpress-extension/compare/v0.7.0...v0.7.1
[0.7.0]: https://github.com/paulgibbs/behat-wordpress-extension/compare/v0.6.0...v0.7.0
[0.6.0]: https://github.com/paulgibbs/behat-wordpress-extension/compare/v0.5.0...v0.6.0
[0.5.0]: https://github.com/paulgibbs/behat-wordpress-extension/compare/v0.4.0...v0.5.0
[0.4.0]: https://github.com/paulgibbs/behat-wordpress-extension/compare/v0.3.0...v0.4.0
[0.3.0]: https://github.com/paulgibbs/behat-wordpress-extension/compare/v0.2.0...v0.3.0
[0.2.0]: https://github.com/paulgibbs/behat-wordpress-extension/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/paulgibbs/behat-wordpress-extension/commit/a47612c6bfd545f6a1dfa854b5080441d93f4514
