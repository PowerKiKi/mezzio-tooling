# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 0.2.0 - 2016-12-20

### Added

- Nothing.

### Changes

- [zendframework/zend-expressive-tooling#7](https://github.com/zendframework/zend-expressive-tooling/pull/7) updates
  the `Mezzio\Tooling\GenerateProgrammaticPipelineFromConfig\Generator`
  class such that it now:

  - Adds dependency configuration for `Mezzio\Middleware\ImplicitHeadMiddleware`
  - Adds dependency configuration for `Mezzio\Middleware\ImplicitOptionsMiddleware`
  - Registers each of the above middleware immediately following the
    routing middleware in the pipeline.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.1.3 - 2016-12-08

### Added

- Nothing.

### Changed

- [zendframework/zend-expressive-tooling#6](https://github.com/zendframework/zend-expressive-tooling/pull/6) provides
  some internal refactoring of `Mezzio\Tooling\GenerateProgrammaticPipelineFromConfig\Generator`
  to optimize performance and maintainability when generating the routing
  statements.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

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
