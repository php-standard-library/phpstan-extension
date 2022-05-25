# PHPStan PSL extension

## Description

The main scope of this extension is to help phpstan to detect the types after using `Psl\Type\shape`.
Its intended to produce the same output as the [psalm plugin](https://github.com/php-standard-library/psalm-plugin).
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

PhpStan assumes that `$input` is of type `array<"age"|"location"|"name", array<"city"|"country"|"state", string>|int|string>`.

If we enable the extension, you will get a more specific and correct type of `array{name: string, age: int, location?: array{city: string, state: string, country: string}}`.
