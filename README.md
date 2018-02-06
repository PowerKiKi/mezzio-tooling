# zend-expressive-tooling

[![Build Status](https://secure.travis-ci.org/zendframework/zend-expressive-tooling.svg?branch=master)](https://secure.travis-ci.org/zendframework/zend-expressive-tooling)
[![Coverage Status](https://coveralls.io/repos/github/zendframework/zend-expressive-tooling/badge.svg?branch=master)](https://coveralls.io/github/zendframework/zend-expressive-tooling?branch=master)

*Migration and development tools for Expressive.*

## Installation

Install via composer:

```bash
$ composer require --dev zendframework/zend-expressive-tooling
```

## `expressive` Tool

- `vendor/bin/expressive`: Entry point for all tooling. Currently exposes the
  following:

  - **middleware:create**: Create an http-interop middleware class file.
  - **migrate:error-middleware-scanner**: Scan for legacy error middleware or
    error middleware invocation.
  - **migrate:interop-middleware**: Migrate interop middlewares and delegators
    to PSR-15 middlewares and request handlers.
  - **migrate:original-messages**: Migrate getOriginal*() calls to request
    attributes.
  - **module:create**: Create and register a middleware module with the
    application.
  - **module:deregister**: Deregister a middleware module from the application.
  - **module:register**: Register a middleware module with the application.
