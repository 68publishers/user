# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.6.0] - 2026-10-28

### Added

- Added Nette Tester tests (DI container with a typical application configuration, `DeviceInfo`, `CancelPasswordRequestPresenterTrait`, `AuthenticatorMount`, `PasswordHasBeenResetEmail`) and GitHub Actions (coding style, tests on PHP 8.3, 8.4 and 8.5 with highest dependencies and on PHP 8.3 with lowest dependencies) and a docker dev environment with PHP 8.3, 8.4 and 8.5.
- Added support for `doctrine/orm` 3, `symfony/event-dispatcher` 8, `matomo/device-detector` 5 and 6.
- Added tests of the entity mapping combined with an annotation-mapped (`doctrine/orm` 2) or attribute-mapped application user entity.
- Declared the directly used dependency `doctrine/orm` (`^2.10 || ^3.0`).

### Changed

- Minimal PHP version is 8.3 (BC break).
- The entities `PasswordRequest` and `DeviceInfo` are mapped with PHP attributes instead of annotations and registered with `EntityMapping::DRIVER_ATTRIBUTE` (BC break: a mapping driver service of the type `Doctrine\ORM\Mapping\Driver\AttributeDriver` is required in the container). The database schema is unchanged.
- Requires `68publishers/doctrine-bridge` `^2.0`, `68publishers/doctrine-persistence` `^0.4`, `68publishers/doctrine-query-objects` `^0.3`, `68publishers/smart-nette-component` `^2.0` and `68publishers/translation-bridge` `^3.0` (BC break).
- Minimal versions are `nette/security` 3.1.8, `nette/application` 3.1, `nette/di` 3.1.3 and `nette/utils` 3.2.10.
- The authenticator service is registered with the type `Nette\Security\Authenticator` instead of the deprecated `Nette\Security\IAuthenticator`. It is now also autowired for `Nette\Security\Authenticator`.
- `DeviceInfo::fill()` creates a new device detector on every call (a static detector kept the User-Agent of the first call).
- `PasswordRequest::$updated` is set in UTC on update (like on creation).
- Replaced deprecated `IAuthenticator` constants, `Strings::contains()` and `PhpLiteral` with `Authenticator::Failure` (etc.), `str_contains()` and `Literal`.
- Migrated PHP-CS-Fixer to v3 (`.php-cs-fixer.dist.php`) and applied it, updated `nette/tester` to `^2.6`, `roave/security-advisories` to `dev-latest`, `contributte/mailing` (dev) to `^0.5 || ^0.6 || ^0.7` and `psr/log` (dev) to `^1.0 || ^2.0 || ^3.0`.

### Removed

- Removed the class `SixtyEightPublishers\User\Common\EventSubscriber\HashUserPasswordSubscriber` (BC break). It was never registered by the bundle and caused a `TypeError` with the `Password` value object, passwords are hashed by the `Password` value object and the `PasswordType`.
- Removed Travis CI and Scrutinizer configurations.

### Fixed

- `DeviceInfo::fill()` no longer fails without the `User-Agent` header or the remote address (e.g. in CLI), and only the first (client) address of `X-Forwarded-For` is stored.
- `CancelPasswordRequestPresenterTrait` uses `getAction()` instead of the deprecated property `$action` (deprecation with `nette/application` 3.2).
- Removed the import of the non-existent class `DoctrineIdentityExtension` in `UserBundleExtension`.
- PHP 8.4 deprecation of the implicitly nullable parameter `$previous` in `PasswordRequestProcessException::__construct()`.

## [0.5.2] - 2024-08-06

### Changed

- Renamed the argument `user` to `userEntity` in the class `PasswordHasBeenResetEmail` due to a collision with the nette/security user in templates.

## [0.5.1] - 2024-08-06

### Changed

- Allowed `nette/utils` v4 and `symfony/event-dispatcher` v7.

## [0.5.0] - 2023-01-06

### Changed

- Support for `nette/security` 3.1 (`IdentityHandler`).

### Removed

- Removed the whole `DoctrineIdentity` namespace, an `IdentityHandler` was added into the `Authentication` namespace.

## [0.4.1] - 2023-01-06

### Fixed

- Fixed password verifying in the Authenticator.

## [0.4.0] - 2023-01-06

### Changed

- The value of the `Password` value object cannot be null.

## [0.3.0] - 2023-01-03

### Changed

- Added PHP 8.1 into `composer.json` and support warning into the README.

## [0.2.4] - 2022-01-25

### Added

- Optional sending of emails about a forgotten password for non-registered users.

## [0.2.3] - 2022-01-21

### Fixed

- Fixed the option `doctrine_identity.namespace`.

## [0.2.2] - 2021-11-04

### Changed

- Forgot password: an error is not logged if a user is not registered.

## [0.2.1] - 2020-12-10

### Changed

- Updated the version of `68publishers/doctrine-bridge`.

## [0.2] - 2020-11-30

### Added

- Reset password: automatic login.
- The main extension `UserBundleExtension` implements `DatabaseTypeProviderInterface`.

### Changed

- Password type and renamed mapping fields.

## [0.1] - 2020-11-23

### Added

- Authentication (authenticator, sign-in control, logout trait, CSRF token factory), forgotten password and Doctrine identity.

## [0.0.0] - 2017-01-12

### Added

- Initial commit.

[Unreleased]: https://github.com/68publishers/user/compare/v0.6.0...HEAD
[0.6.0]: https://github.com/68publishers/user/compare/v0.5.2...v0.6.0
[0.5.2]: https://github.com/68publishers/user/compare/v0.5.1...v0.5.2
[0.5.1]: https://github.com/68publishers/user/compare/v0.5.0...v0.5.1
[0.5.0]: https://github.com/68publishers/user/compare/v0.4.1...v0.5.0
[0.4.1]: https://github.com/68publishers/user/compare/v0.4.0...v0.4.1
[0.4.0]: https://github.com/68publishers/user/compare/v0.3.0...v0.4.0
[0.3.0]: https://github.com/68publishers/user/compare/v0.2.4...v0.3.0
[0.2.4]: https://github.com/68publishers/user/compare/v0.2.3...v0.2.4
[0.2.3]: https://github.com/68publishers/user/compare/v0.2.2...v0.2.3
[0.2.2]: https://github.com/68publishers/user/compare/v0.2.1...v0.2.2
[0.2.1]: https://github.com/68publishers/user/compare/v0.2...v0.2.1
[0.2]: https://github.com/68publishers/user/compare/v0.1...v0.2
[0.1]: https://github.com/68publishers/user/compare/v0.0.0...v0.1
[0.0.0]: https://github.com/68publishers/user/releases/tag/v0.0.0
