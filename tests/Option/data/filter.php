<?php // lint >= 8.1

declare(strict_types=1);

namespace PslOptionTest;

use Closure;
use Psl\Option;
use Psl\Type;

use function PHPStan\Testing\assertType;


function positive_int(int $value): void
{
	$option = Option\some($value);
	$option = $option->filter(fn($value) => $value > 0);
	assertType('Psl\Option\Option<int<1, max>>', $option);

	$option = Option\some($value);
	$option = $option->filter(Type\positive_int()->matches(...));
	assertType('Psl\Option\Option<int<1, max>>', $option);

	assertType(
		'Psl\Option\Option<*NEVER*>',
		$option->filter(Type\literal_scalar(0)->matches(...))
	);
}

function non_empty_string(string $value): void
{
	$option = Option\some($value);
	$option = $option->filter(fn($value) => '' !== $value);
	assertType('Psl\Option\Option<non-empty-string>', $option);

	$option = Option\some($value);
	$option = $option->filter(Type\non_empty_string()->matches(...));
	assertType('Psl\Option\Option<non-empty-string>', $option);

	assertType(
		'Psl\Option\Option<non-empty-string>',
		$option->filter(Type\string()->matches(...))
	);

	assertType(
		'Psl\Option\Option<*NEVER*>',
		$option->filter(Type\literal_scalar('')->matches(...))
	);
}


function numeric_string(string $value): void
{
	$option = Option\some($value);
	$option = $option->filter(fn($value) => is_numeric($value));
	assertType('Psl\Option\Option<numeric-string>', $option);

	$option = Option\some($value);
	$option = $option->filter(is_numeric(...));
	assertType('Psl\Option\Option<numeric-string>', $option);

	$option = Option\some($value);
	$option = $option->filter(Type\numeric_string()->matches(...));
	assertType('Psl\Option\Option<numeric-string>', $option);

	assertType(
		'Psl\Option\Option<*NEVER*>',
		$option->filter(Type\int()->matches(...))
	);
}

function literal_string(string $value): void
{
	$option = Option\some($value);
	$option = $option->filter(fn($value) => 'potato' === $value);
	assertType('Psl\Option\Option<\'potato\'>', $option);

	$option = Option\some($value);
	$option = $option->filter(fn ($value) => 'potato' === $value || 'tomato' === $value);
	assertType('Psl\Option\Option<\'potato\'|\'tomato\'>', $option);

	$option = Option\some($value);
	$option = $option->filter(fn ($value) => in_array($value, ['potato', 'tomato'], true));
	assertType('Psl\Option\Option<\'potato\'|\'tomato\'>', $option);

	assertType(
		'Psl\Option\Option<*NEVER*>',
		$option->filter(Type\int()->matches(...))
	);

	assertType(
		'Psl\Option\Option<\'potato\'>',
		$option->filter(Type\literal_scalar('potato')->matches(...))
	);

	assertType(
		'Psl\Option\Option<\'potato\'|\'tomato\'>',
		$option->filter(Type\string()->matches(...))
	);

	$option = Option\some($value);
	$option = $option->filter(Type\literal_scalar('potato')->matches(...));
	assertType('Psl\Option\Option<\'potato\'>', $option);
}


/**
 * @param list<float> $value
 */
function filter_list(array $value): void
{
	$option = Option\some($value);
	assertType('Psl\Option\Option<list<float>>', $option);
	$option = $option->filter(fn($value) => [] !== $value);
	assertType('Psl\Option\Option<non-empty-list<float>>', $option);

	$option = Option\some($value);
	assertType('Psl\Option\Option<list<float>>', $option);
	$option = $option->filter(Type\non_empty_vec(Type\float())->matches(...));
	assertType('Psl\Option\Option<non-empty-list<float>>', $option);
}

