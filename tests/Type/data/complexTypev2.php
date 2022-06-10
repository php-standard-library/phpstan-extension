<?php declare(strict_types=1);

namespace PslComplexV2Test;

use Psl\Type;

use function PHPStan\Testing\assertType;


/**
 * For PSL >= 2.0.0
 */
class ComplexTypesV2
{
	public function coerceShapeWithComplexTypes($input): void
	{
		$intNullOrString = Type\union(Type\int(), Type\nullable(Type\string()));
		$bikeAndPlane = Type\intersection(Type\instance_of(Bike::class), Type\instance_of(Plane::class));
		$shape = Type\shape([
			'name_or_length' => $intNullOrString,
			'transportation' => $bikeAndPlane,
			'something' => Type\union($intNullOrString, $bikeAndPlane)
		]);

		$output = $shape->coerce($input);
		assertType('array{name_or_length: int|string|null, transportation: PslComplexV2Test\Bike&PslComplexV2Test\Plane, something: int|(PslComplexV2Test\Bike&PslComplexV2Test\Plane)|string|null}', $output);
	}
}
