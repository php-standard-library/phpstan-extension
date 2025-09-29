<?php

declare(strict_types=1);

namespace PslOptionTest;

use Closure;
use Psl\Option;

use function PHPStan\Testing\assertType;


function positive_int(int $value): void
{
	$option = Option\some($value);
	$option = $option->filter(fn($value) => $value > 0);
	assertType('Psl\Option\Option<int<1, max>>', $option);

	$option = Option\some($value);
	$option = $option->filter(Closure::fromCallable([\Psl\Type\positive_int(), 'matches']));
	assertType('Psl\Option\Option<int<1, max>>', $option);
}

function non_empty_string(string $value): void
{
	$option = Option\some($value);
	$option = $option->filter(fn($value) => '' !== $value);
	assertType('Psl\Option\Option<non-empty-string>', $option);

	$option = Option\some($value);
	$option = $option->filter(Closure::fromCallable([\Psl\Type\non_empty_string(), 'matches']));
	assertType('Psl\Option\Option<non-empty-string>', $option);
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
	$option = $option->filter(Closure::fromCallable([\Psl\Type\numeric_string(), 'matches']));
	assertType('Psl\Option\Option<numeric-string>', $option);
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

	$option = Option\some($value);
	$option = $option->filter(Closure::fromCallable([\Psl\Type\literal_scalar('potato'), 'matches']));
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
	$option = $option->filter(Closure::fromCallable([\Psl\Type\non_empty_vec(), 'matches']));
	assertType('Psl\Option\Option<non-empty-list<float>>', $option);
}

