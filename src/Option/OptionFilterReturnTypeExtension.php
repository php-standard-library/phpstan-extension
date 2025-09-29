<?php declare(strict_types = 1);

namespace Psl\PHPStan\Option;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Node\Expr\TypeExpr;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\Type;
use Psl\Option\Option;

class OptionFilterReturnTypeExtension implements DynamicMethodReturnTypeExtension
{

	public function getClass(): string
	{
		return Option::class;
	}

	public function isMethodSupported(MethodReflection $methodReflection): bool
	{
		return $methodReflection->getName() === 'filter';
	}

	public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): ?Type
	{
		$args = $methodCall->getArgs();
		if (!isset($args[0])) {
			return null;
		}
		$filterCallback = $args[0]->value;

		$optionType = $scope->getType($methodCall->var);
		$originalType = $optionType->getTemplateType('Psl\Option\Option', 'T');

		$refinedType = $this->analyzeFilterCallback($filterCallback, $originalType, $scope);

		return new GenericObjectType(
			'Psl\Option\Option',
			[$refinedType]
		);
	}

	private function analyzeFilterCallback(Expr $filterCallback, Type $originalType, Scope $scope): Type
	{
		$arrayType = new ArrayType(new IntegerType(), $originalType);

		$refinedType = $scope
			->getType(
				new FuncCall(
					new Name('array_filter'),
					[new Arg(new TypeExpr($arrayType)), new Arg($filterCallback)]
				)
			)
			->getIterableValueType();

		if (!$refinedType->equals($originalType)) {
			return $refinedType;
		}

		return $originalType;
	}

}
