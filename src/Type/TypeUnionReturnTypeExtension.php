<?php declare(strict_types = 1);

namespace Psl\PHPStan\Type;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\DynamicFunctionReturnTypeExtension;
use PHPStan\Type\ErrorType;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use Psl\Type\TypeInterface;

class TypeUnionReturnTypeExtension implements DynamicFunctionReturnTypeExtension
{

	public function isFunctionSupported(FunctionReflection $functionReflection): bool
	{
		return $functionReflection->getName() === 'Psl\Type\union';
	}

	public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): ?Type
	{
		$args = $functionCall->getArgs();
		if ($args === []) {
			return null;
		}

		$innerTypes = [];
		foreach ($args as $arg) {
			$argType = $scope->getType($arg->value);
			$inner = $argType->getTemplateType(TypeInterface::class, 'T');
			if ($inner instanceof ErrorType) {
				return null;
			}
			$innerTypes[] = $inner;
		}

		return new GenericObjectType(
			TypeInterface::class,
			[
				TypeCombinator::union(...$innerTypes),
			]
		);
	}

}
