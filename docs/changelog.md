# Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [semantic versioning](http://semver.org/).

## [Unreleased]
### Deprecated
- `is_wordpress_error()` moved into `Util` namespace

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
- Introduce sensiolabs/behat-page-object-extension for future development.

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

[0.5.0]: https://github.com/paulgibbs/behat-wordpress-extension/compare/v0.4.0...v0.5.0
[0.4.0]: https://github.com/paulgibbs/behat-wordpress-extension/compare/v0.3.0...v0.4.0
[0.3.0]: https://github.com/paulgibbs/behat-wordpress-extension/compare/v0.2.0...v0.3.0
[0.2.0]: https://github.com/paulgibbs/behat-wordpress-extension/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/paulgibbs/behat-wordpress-extension/commit/a47612c6bfd545f6a1dfa854b5080441d93f4514
