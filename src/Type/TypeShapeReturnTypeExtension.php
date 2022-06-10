<?php declare(strict_types = 1);

namespace Psl\PHPStan\Type;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\Constant\ConstantArrayTypeBuilder;
use PHPStan\Type\DynamicFunctionReturnTypeExtension;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\TypeUtils;
use PHPStan\Type\TypeWithClassName;
use Psl\Type\Internal\OptionalType;
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
		$args = $functionCall->getArgs();
		if (!isset($args[0])) {
			return null;
		}

		$arg = $scope->getType($args[0]->value);
		$arrays = TypeUtils::getConstantArrays($arg);
		if (count($arrays) === 0) {
			return null;
		}

		$results = [];
		foreach ($arrays as $array) {
			$result = $this->createResult($array);
			if ($result === null) {
				return null;
			}

			$results[] = $result;
		}

		return new GenericObjectType(
			TypeInterface::class,
			[
				TypeCombinator::union(...$results),
			]
		);
	}

	private function createResult(ConstantArrayType $arrayType): ?Type
	{
		$builder = ConstantArrayTypeBuilder::createEmpty();
		foreach ($arrayType->getKeyTypes() as $key) {
			$valueType = $arrayType->getOffsetValueType($key);
			if (!$valueType instanceof TypeWithClassName) {
				return null;
			}

			$valueClassReflection = $valueType->getClassReflection();
			if ($valueClassReflection === null) {
				return null;
			}

			$typeInterfaceAncestor = $valueClassReflection->getAncestorWithClassName(TypeInterface::class);
			if ($typeInterfaceAncestor === null) {
				return null;
			}

			$typeMap = $typeInterfaceAncestor->getActiveTemplateTypeMap();
			$t = $typeMap->getType('T');
			if ($t === null) {
				return null;
			}

			[$type, $optional] = $this->extractOptional($t);

			$builder->setOffsetValueType($key, $type, $optional);
		}

		return $builder->getArray();
	}

	/**
	 * @return array{Type, bool}
	 */
	private function extractOptional(Type $type): array
	{
		if (!$type instanceof TypeWithClassName) {
			return [$type, false];
		}

		$classReflection = $type->getClassReflection();
		if ($classReflection === null) {
			return [$type, false];
		}
		$optionalTypeAncestor = $classReflection->getAncestorWithClassName(OptionalType::class);
		if ($optionalTypeAncestor === null) {
			return [$type, false];
		}

		$typeMap = $optionalTypeAncestor->getActiveTemplateTypeMap();
		$t = $typeMap->getType('T');
		if ($t === null) {
			return [$type, false];
		}

		return [$t, true];
	}

}
