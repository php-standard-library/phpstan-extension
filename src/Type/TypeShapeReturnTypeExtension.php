<?php declare(strict_types = 1);

namespace Psl\PHPStan\Type;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\Constant\ConstantArrayTypeBuilder;
use PHPStan\Type\DynamicFunctionReturnTypeExtension;
use PHPStan\Type\ErrorType;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\TypeUtils;
use PHPStan\Type\UnionType;
use Psl\Type\TypeInterface;
use function count;

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
		$builder = ConstantArrayTypeBuilder::createEmpty();
		foreach ($arrayType->getKeyTypes() as $key) {
			$valueType = $arrayType->getOffsetValueType($key);
			$optional = false;
			if ($valueType instanceof UnionType) {
				$valueType = $valueType->getTypes()[0];
				$optional = true;
			}

			if ($valueType instanceof GenericObjectType && $valueType->accepts($typeInterfaceType, $scope->isDeclareStrictTypes())->yes()) {
				$builder->setOffsetValueType($key, $valueType->getTypes()[0], $optional);
				continue;
			}

			return new ErrorType();
		}

		return $builder->getArray();
	}

}
