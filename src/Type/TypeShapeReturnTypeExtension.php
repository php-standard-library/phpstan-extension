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
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use Psl\Type\Internal\NullishType;
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
		$arrays = $arg->getConstantArrays();
		if (count($arrays) === 0) {
			return null;
		}

		$results = [];
		foreach ($arrays as $array) {
			$results[] = $this->createResult($array);
		}

		return new GenericObjectType(
			TypeInterface::class,
			[
				TypeCombinator::union(...$results),
			]
		);
	}

	private function createResult(ConstantArrayType $arrayType): Type
	{
		$builder = ConstantArrayTypeBuilder::createEmpty();
		foreach ($arrayType->getKeyTypes() as $key) {
			$valueType = $arrayType->getOffsetValueType($key);
			$templateType = $valueType->getTemplateType(TypeInterface::class, 'T');
			[$type, $optional] = $this->extractOptional($templateType);
			[$type] = $this->extractNullish($type);

			$builder->setOffsetValueType($key, $type, $optional);
		}

		return $builder->getArray();
	}

	/**
	 * @return array{Type, bool}
	 */
	private function extractNullish(Type $type): array
	{
		$nullishType = $type->getTemplateType(NullishType::class, 'T');
		if ($nullishType instanceof ErrorType) {
			return [$type, false];
		}

		return [TypeCombinator::addNull($nullishType), false];
	}

	/**
	 * @return array{Type, bool}
	 */
	private function extractOptional(Type $type): array
	{
		$optionalType = $type->getTemplateType(OptionalType::class, 'T');
		if ($optionalType instanceof ErrorType) {
			return [$type, false];
		}

		return [$optionalType, true];
	}

}
