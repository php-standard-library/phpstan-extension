<?php declare(strict_types=1);

namespace PslShapeTest;

use Psl\Type;

use function PHPStan\Testing\assertType;

class NullishAssertTest
{
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
}
