<?php declare(strict_types=1);

namespace PslShapeTest;

use Psl\Type;

use function PHPStan\Testing\assertType;

class AssertTest
{
	/**
	 * @param array<mixed> $a
	 */
	public function assertShape(array $a): void
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

		$b = $specification->assert($a);

		assertType('array{name: string, age: int, location?: array{city: string, state: string, country: string}}', $a);
		assertType('array{name: string, age: int, location?: array{city: string, state: string, country: string}}', $b);
	}

	/**
	 * @param array<mixed> $a
	 */
	public function assertNullishShape(array $a): void
	{
		$specification = Type\shape([
			'name' => Type\string(),
			'bio' => Type\nullish(Type\string()),
		]);

		$b = $specification->assert($a);

		assertType('array{name: string, bio: string|null}', $a);
		assertType('array{name: string, bio: string|null}', $b);
	}

	/**
	 * @param array<mixed> $a
	 */
	public function assertOptionalNullishShape(array $a): void
	{
		$specification = Type\shape([
			'bio' => Type\optional(Type\nullish(Type\string())),
		]);

		$b = $specification->assert($a);

		assertType('array{bio?: string|null}', $a);
		assertType('array{bio?: string|null}', $b);
	}

	public function assertInt($i): void
	{
		$spec = Type\int();
		$j = $spec->assert($i);
		assertType('int', $i);
		assertType('int', $j);
	}

	public function assertWrong($i): void
	{
		$spec = Type\int();
		$j = $spec->assert();
		assertType('mixed', $i);
		assertType('int', $j);
	}
}
