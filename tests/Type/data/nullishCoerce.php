<?php declare(strict_types=1);

namespace PslShapeTest;

use Psl\Type;

use function PHPStan\Testing\assertType;

class NullishCoerceTest
{
	/**
	 * @param array<mixed> $input
	 */
	public function coerceNullishShape(array $input): void
	{
		$specification = Type\shape([
			'name' => Type\string(),
			'bio' => Type\nullish(Type\string()),
		]);

		$output = $specification->coerce($input);

		assertType('array{name: string, bio: string|null}', $output);
	}

	/**
	 * @param array<mixed> $input
	 */
	public function coerceOptionalNullishShape(array $input): void
	{
		$specification = Type\shape([
			'bio' => Type\optional(Type\nullish(Type\string())),
		]);

		$output = $specification->coerce($input);

		assertType('array{bio?: string|null}', $output);
	}
}
