<?php declare(strict_types=1);

namespace PslComplexV1Test;

use Psl\Type;

use function PHPStan\Testing\assertType;

interface Bike {}
interface Plane {}

/**
 * For PSL < 2.0.0
 */
class ComplexTypesV1
{

	public function coerceShapeWithComplexTypes($input): void
	{
		$intNullOrString = Type\union(Type\int(), Type\nullable(Type\string()));
		$bikeAndPlane = Type\intersection(Type\object(Bike::class), Type\object(Plane::class));
		$shape = Type\shape([
			'name_or_length' => $intNullOrString,
			'transportation' => $bikeAndPlane,
			'something' => Type\union($intNullOrString, $bikeAndPlane)
		]);

		$output = $shape->coerce($input);
		assertType('array{name_or_length: int|string|null, transportation: PslComplexV1Test\Bike&PslComplexV1Test\Plane, something: int|(PslComplexV1Test\Bike&PslComplexV1Test\Plane)|string|null}', $output);
	}

}
