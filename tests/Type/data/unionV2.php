<?php declare(strict_types=1);

namespace PslUnionV2Test;

use Psl\Type;

use function PHPStan\Testing\assertType;

/**
 * For PSL >= 2.0.0 (literal_scalar)
 */
class UnionTypes
{
	public function literalScalarUnion($input): void
	{
		$ab = Type\union(Type\literal_scalar('a'), Type\literal_scalar('b'));
		$out = $ab->coerce($input);
		assertType("'a'|'b'", $out);
	}

	public function mixedUnion($input): void
	{
		$intOrString = Type\union(Type\int(), Type\string());
		$out = $intOrString->coerce($input);
		assertType('int|string', $out);
	}

	public function shapeWithLiteralUnion($input): void
	{
		$shape = Type\shape([
			'kind' => Type\union(
				Type\literal_scalar('a'),
				Type\literal_scalar('b'),
				Type\literal_scalar('c'),
			),
		]);
		$out = $shape->coerce($input);
		assertType("array{kind: 'a'|'b'|'c'}", $out);
	}

	public function singleArgUnion($input): void
	{
		$single = Type\union(Type\int());
		$out = $single->coerce($input);
		assertType('int', $out);
	}
}
