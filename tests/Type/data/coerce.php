<?php declare(strict_types=1);

namespace PslShapeTest;

use Psl\Type;

use function PHPStan\Testing\assertType;

class GeneralTest
{
    /**
     * @param array<mixed> $input
     */
    public function coerceShape(array $input): void
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

	public function coerceInt($i): void
	{
		$spec = Type\int();
		$coerced = $spec->coerce($i);
		assertType('int', $coerced);
	}
}
