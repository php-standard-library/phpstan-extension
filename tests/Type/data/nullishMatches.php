<?php declare(strict_types=1);

namespace PslShapeTest;

use Psl\Type;

use function PHPStan\Testing\assertType;

class NullishMatchesTest
{
	/**
	 * @param array<mixed> $a
	 */
	public function matchesNullishShape(array $a): void
	{
		$specification = Type\shape([
			'name' => Type\string(),
			'bio' => Type\nullish(Type\string()),
		]);

		if ($specification->matches($a)) {
			assertType('array{name: string, bio: string|null}', $a);
		} else {
			assertType('array<mixed>', $a);
		}
	}

	/**
	 * @param array<mixed> $a
	 */
	public function matchesOptionalNullishShape(array $a): void
	{
		$specification = Type\shape([
			'bio' => Type\optional(Type\nullish(Type\string())),
		]);

		if ($specification->matches($a)) {
			assertType('array{bio?: string|null}', $a);
		} else {
			assertType('non-empty-array<mixed>', $a);
		}
	}
}
