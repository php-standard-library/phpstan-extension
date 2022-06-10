# PHPStan PSL extension

[![Build](https://github.com/php-standard-library/phpstan-extension/workflows/Build/badge.svg)](https://github.com/php-standard-library/phpstan-extension/actions)
[![Latest Stable Version](https://poser.pugx.org/php-standard-library/phpstan-extension/v/stable)](https://packagist.org/packages/php-standard-library/phpstan-extension)
[![License](https://poser.pugx.org/php-standard-library/phpstan-extension/license)](https://packagist.org/packages/php-standard-library/phpstan-extension)

* [PHPStan](https://phpstan.org/)
* [PSL](https://github.com/azjezz/psl)

## Description

The main goal of this extension is to help PHPStan to detect the types after using `Psl\Type\shape`.

Given the following example:

```php
use Psl\Type;

$specification = Type\shape([
    'name' => Type\string(),
    'age' => Type\int(),
    'location' => Type\optional(Type\shape([
        'city' => Type\string(),
        'state' => Type\string(),
        'country' => Type\string(),
    ]))
]);

$input = $specification->coerce($_GET['user']);
```

PHPStan assumes that `$input` is of type `array<"age"|"location"|"name", array<"city"|"country"|"state", string>|int|string>`.

If we enable the extension, you will get a more specific and correct type of `array{name: string, age: int, location?: array{city: string, state: string, country: string}}`.

Besides coerce, this extension also supports `matches()` and `assert()` methods.

## Installation

To use this extension, require it in [Composer](https://getcomposer.org/):

```
composer require --dev php-standard-library/phpstan-extension
```

If you also install [phpstan/extension-installer](https://github.com/phpstan/extension-installer) then you're all set!

<details>
  <summary>Manual installation</summary>

If you don't want to use `phpstan/extension-installer`, include extension.neon in your project's PHPStan config:

```
includes:
    - vendor/php-standard-library/phpstan-extension/extension.neon
```
</details>
