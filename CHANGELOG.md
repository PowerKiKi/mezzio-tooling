# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 0.4.1 - 2017-04-11

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Updates laminas-component-installer minimum version to 0.7.1, which provides a
  fix for detection of config providers; prior to this fix, `module:degister`
  could not remove globally qualified config providers for a module.

## 0.4.0 - 2017-04-11

### Added

- [zendframework/zend-expressive-tooling#22](https://github.com/zendframework/zend-expressive-tooling/pull/22) and
  [zendframework/zend-expressive-tooling#23](https://github.com/zendframework/zend-expressive-tooling/pull/23) add
  the script `mezzio`, which allows executing any of the other commands
  provided in the package, including a new command for middleware creation. The
  exposed commands are:

  - **middleware:create**: Create an http-interop middleware class file.
  - **migrate:error-middleware-scanner**: Scan for legacy error middleware or error middleware invocation.
  - **migrate:original-messages**: Migrate getOriginal*() calls to request attributes.
  - **migrate:pipeline**: Generate a programmatic pipeline and routes from configuration.
  - **module:create**: Create and register a middleware module with the application
  - **module:deregister**: Deregister a middleware module from the application
  - **module:register**: Register a middleware module with the application

  All previous scripts (e.g., `mezzio-pipeline-from-config`) are still
  present and continue to work, but are deprecated in favor of the `mezzio`
  script.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.3.2 - 2017-03-13

### Added

- Nothing.

### Changed

- [zendframework/zend-expressive-tooling#17](https://github.com/zendframework/zend-expressive-tooling/pull/17)
  changes the reference to the `DefaultDelegate` in the generated
  `config/autoload/programmatic-pipeline.global.php` to be a string instead of
  using `::class` notation. Using a string name makes it clear the service is
  not a concrete class or interface name.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-expressive-tooling#16](https://github.com/zendframework/zend-expressive-tooling/pull/16) fixes
  generation of routes where no HTTP method is specified to use a `null` instead
  of the `Mezzio\Router\Route::HTTP_METHOD_ANY` constant.

## 0.3.1 - 2017-03-02

### Added

- [zendframework/zend-expressive-tooling#15](https://github.com/zendframework/zend-expressive-tooling/pull/15) adds
  documentation for the `mezzio-module` command to the README file.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-expressive-tooling#14](https://github.com/zendframework/zend-expressive-tooling/pull/14) fixes
  the `public/index.php` template to remove the `error_reporting()` declaration,
  as it is no longer necessary with Stratigily 2 and the upcoming Mezzio 2
  release.

## 0.3.0 - 2017-03-01

### Added

- [zendframework/zend-expressive-tooling#12](https://github.com/zendframework/zend-expressive-tooling/pull/12) adds
  the new tool `mezzio-module`, with the commands `create`, `register`, and
  `deregister`, for creating new "modules". `create` will create a tree under
  the `src/` tree named for the provided module containing `src/` and
  `templates/` subdirectories, as well as a `ConfigProvider` class; it then adds
  an entry for the `ConfigProvider` to the application configuration, and an
  autoloading entry to `composer.json`. `register` will register an existing
  module with the application configuration, and, if necessary, enable
  autoloading for it with `composer.json`. `deregister` does the opposite of
  `register`, without removing any files from the source tree. Use the command's
  `help`, `--help`, or `-h` options for full usage details.

### Changes

- [zendframework/zend-expressive-tooling#10](https://github.com/zendframework/zend-expressive-tooling/pull/10)
  updates the `mezzio-pipeline-from-config` tooling to no longer generate
  `pipeErrorHandler()` statements. It will now notify users via STDOUT if
  legacy error handlers are encountered, indicating which were encountered.

- [zendframework/zend-expressive-tooling#10](https://github.com/zendframework/zend-expressive-tooling/pull/10)
  updates the `mezzio-pipeline-from-config` tooling to now register the
  `DefaultDelegate` and `NotFoundDelegate` services, with the former aliased to
  the latter.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

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
