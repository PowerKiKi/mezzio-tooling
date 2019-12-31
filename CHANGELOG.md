# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 0.1.2 - 2016-12-07

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-expressive-tooling#1](https://github.com/zendframework/zend-expressive-tooling/pull/1) updates
  the various `Help` classes to translate a command name to be relative to the
  `vendor/bin/` directory under every operating system when run local to a
  project.
- [zendframework/zend-expressive-tooling#3](https://github.com/zendframework/zend-expressive-tooling/pull/3) fixes
  the top-level key used in generated configuration files to properly be
  `api-tools-mezzio` instead of `api-tools-mezzio-tooling`.
- [zendframework/zend-expressive-tooling#5](https://github.com/zendframework/zend-expressive-tooling/pull/5) fixes
  the help message for the `mezzio-pipeline-from-config` command to detail
  what it actually does (vs what the original incarnation did).

## 0.1.1 - 2016-12-06

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Fixed the namespace declarations of all vendor binaries to ensure each points
  to the correct tooling namespace for the command being invoked.

## 0.1.0 - 2016-12-06

- Initial release.

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.
