<?php declare(strict_types=1);

namespace PslShapeTest;

use Psl\Type;

use function PHPStan\Testing\assertType;

class MatchesTest
{
	/**
	 * @param array<mixed> $a
	 */
	public function matchesShape(array $a): void
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

		if ($specification->matches($a)) {
			assertType('array{name: string, age: int, location?: array{city: string, state: string, country: string}}', $a);
		} else {
			assertType('array', $a);
		}
	}

	public function matchesInt($i): void
	{
		$spec = Type\int();
		if ($spec->matches($i)) {
			assertType('int', $i);
		} else {
			assertType('mixed~int', $i);
		}
	}

	public function matchesWrong($i): void
	{
		$spec = Type\int();
		if ($spec->matches()) {

		} else {

		}
	}
}
