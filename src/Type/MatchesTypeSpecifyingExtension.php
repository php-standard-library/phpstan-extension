<?php declare(strict_types = 1);

namespace Psl\PHPStan\Type;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Analyser\SpecifiedTypes;
use PHPStan\Analyser\TypeSpecifier;
use PHPStan\Analyser\TypeSpecifierAwareExtension;
use PHPStan\Analyser\TypeSpecifierContext;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\MethodTypeSpecifyingExtension;
use PHPStan\Type\TypeWithClassName;
use Psl\Type\TypeInterface;

class MatchesTypeSpecifyingExtension implements MethodTypeSpecifyingExtension, TypeSpecifierAwareExtension
{

	private TypeSpecifier $typeSpecifier;

	public function setTypeSpecifier(TypeSpecifier $typeSpecifier): void
	{
		$this->typeSpecifier = $typeSpecifier;
	}

	public function getClass(): string
	{
		return TypeInterface::class;
	}

	public function isMethodSupported(MethodReflection $methodReflection, MethodCall $node, TypeSpecifierContext $context): bool
	{
		return !$context->null() && $methodReflection->getName() === 'matches';
	}

	public function specifyTypes(MethodReflection $methodReflection, MethodCall $node, Scope $scope, TypeSpecifierContext $context): SpecifiedTypes
	{
		$args = $node->getArgs();
		if (!isset($args[0])) {
			return new SpecifiedTypes();
		}

		$specType = $scope->getType($node->var);
		if (!$specType instanceof TypeWithClassName) {
			return new SpecifiedTypes();
		}

		$specTypeReflection = $specType->getClassReflection();
		if ($specTypeReflection === null) {
			return new SpecifiedTypes();
		}

		$typeInterfaceAncestor = $specTypeReflection->getAncestorWithClassName(TypeInterface::class);
		if ($typeInterfaceAncestor === null) {
			return new SpecifiedTypes();
		}

		$typeMap = $typeInterfaceAncestor->getActiveTemplateTypeMap();
		$t = $typeMap->getType('T');
		if ($t === null) {
			return new SpecifiedTypes();
		}

		return $this->typeSpecifier->create(
			$args[0]->value,
			$t,
			$context
		);
	}

}
