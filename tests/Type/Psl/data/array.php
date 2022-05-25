<?php

declare(strict_types=1);

namespace PHPStan\Type\Psl;

use Psl\Type;

use function PHPStan\Testing\assertType;

class GeneralTest
{
    /**
     * @param array<mixed> $input
     */
    public function coerceEasy(array $input): void
    {
        $specification = Type\shape([
            'name' => Type\string(),
            'age' => Type\int(),
            'location' => Type\optional(Type\shape([
                'city' => Type\string(),
                'state' => Type\string(),
                'country' => Type\string(),
            ])),
        ]);

        $input = $specification->coerce($input);

        assertType('array{name: string, age: int, location?: array{city: string, state: string, country: string}}', $input);
    }
}
