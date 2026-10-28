> :warning: Warning! This package does not have active support, it exists only for the historical needs of the author.

# User

[![Total Downloads][ico-downloads]][link-downloads]
[![Latest Version on Packagist][ico-version]][link-packagist]

The package adds support for a database-backed user system for Nette. It provides a user management that aims to handle common tasks such as user registration, login, password retrieval and much more.

## Installation

The best way to install 68publishers/user is using Composer:

```bash
composer require 68publishers/user
```

Requirements: PHP `^8.3`, `doctrine/orm` `^2.10 || ^3.0`, `nette/security` `^3.1.8`.

The entities are mapped with PHP attributes and registered through `68publishers/doctrine-bridge` with the attribute driver (`EntityMapping::DRIVER_ATTRIBUTE`), so a mapping driver service of the type `Doctrine\ORM\Mapping\Driver\AttributeDriver` must exist in the container. Applications that map their own entities with annotations (`doctrine/orm` 2) only need to register it next to the annotation driver, e.g.:

```neon
services:
    attributeDriver:
        factory: Doctrine\ORM\Mapping\Driver\AttributeDriver([], true)
        autowired: false
```

or use `Nettrine\ORM\DI\OrmAttributesExtension` of `nettrine/orm` (the bridge option `68publishers.doctrine_bridge.services.drivers.attribute` selects the driver service if there are more of them). Create the driver with `reportFieldsWhereDeclared: true` (the second argument, the only mode of `doctrine/orm` 3, `false` is deprecated since `doctrine/orm` 2.16). Annotation-mapped application entities can still relate to the package entities (`PasswordRequest` relates to your user entity through the resolved `UserInterface`).

## Upgrade

### From 0.5 to 0.6

- The minimal PHP version is 8.3.
- The package requires `68publishers/doctrine-bridge` `^2.0`, `68publishers/doctrine-persistence` `^0.4`, `68publishers/doctrine-query-objects` `^0.3`, `68publishers/smart-nette-component` `^2.0` and `68publishers/translation-bridge` `^3.0`.
- The class `SixtyEightPublishers\User\Common\EventSubscriber\HashUserPasswordSubscriber` was removed. It was never registered by the bundle, passwords are hashed by the `Password` value object and the `PasswordType`.
- The entities (`PasswordRequest`, `DeviceInfo`) are mapped with PHP attributes instead of annotations (the database schema is the same) and `doctrine/orm` 3 is supported. Register a service of the type `Doctrine\ORM\Mapping\Driver\AttributeDriver` (see [Installation](#installation)), otherwise the container compilation fails with "Can't resolve service definition ... services.drivers.attribute".
- `doctrine/annotations` is no longer required.
- The authenticator service is registered with the type `Nette\Security\Authenticator` (previously the deprecated `IAuthenticator`), so it is autowired for both interfaces.

## Usage

```php
// @todo
```

## Rules for contributing

- **1 PR per feature**
- PR with tests are more likely to be merged
- **tests and coding standard must pass**

```bash
vendor/bin/tester ./tests -s
vendor/bin/php-cs-fixer fix -v --dry-run
```

[ico-version]: https://img.shields.io/packagist/v/68publishers/user.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/68publishers/user.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/68publishers/user
[link-downloads]: https://packagist.org/packages/68publishers/user
