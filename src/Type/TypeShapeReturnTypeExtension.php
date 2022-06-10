<?php declare(strict_types = 1);

namespace Psl\PHPStan\Type;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\Constant\ConstantIntegerType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\DynamicFunctionReturnTypeExtension;
use PHPStan\Type\ErrorType;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\TypeUtils;
use PHPStan\Type\UnionType;
use Psl\Type\TypeInterface;
use function array_keys;
use function array_map;
use function array_values;
use function count;
use function is_string;

class TypeShapeReturnTypeExtension implements DynamicFunctionReturnTypeExtension
{

	public function isFunctionSupported(FunctionReflection $functionReflection): bool
	{
		return $functionReflection->getName() === 'Psl\Type\shape';
	}

	public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): ?Type
	{
		$arg = $scope->getType($functionCall->getArgs()[0]->value);
		$arrays = TypeUtils::getConstantArrays($arg);
		if (count($arrays) === 0) {
			return null;
		}

		$results = [];
		foreach ($arrays as $array) {
			$results[] = $this->createResult($scope, $array);
		}

		return new GenericObjectType(
			TypeInterface::class,
			[
				TypeCombinator::union(...$results),
			]
		);
	}

	private function createResult(Scope $scope, ConstantArrayType $arrayType): Type
	{
		$typeInterfaceType = new ObjectType(TypeInterface::class);
		$properties = [];
		$optionalKeys = [];
		foreach ($arrayType->getKeyTypes() as $i => $key) {
			$realKey = $key->getValue();
			$valueType = $arrayType->getOffsetValueType($key);
			recheck:

			if ($valueType instanceof GenericObjectType && $valueType->accepts($typeInterfaceType, $scope->isDeclareStrictTypes())->yes()) {
				$properties[$realKey] = $valueType->getTypes()[0];
				continue;
			}

			if ($valueType instanceof UnionType) {
				$valueType = $valueType->getTypes()[0];
				$optionalKeys[] = $i;
				goto recheck;
			}

			return new ErrorType();
		}

		$keys = array_map(
			static fn ($key) => is_string($key) ? new ConstantStringType($key) : new ConstantIntegerType($key),
			array_keys($properties)
		);

		return new ConstantArrayType(
			$keys,
			array_values($properties),
			[0],
			$optionalKeys
		);
	}

}
