<?php

namespace PslBug9Test;

use Psl\Type;
use function PHPStan\Testing\assertType;

/**
 * @param mixed $input
 */
function checkShape($input): void
{
	$output = Type\shape([
		'foo' => Type\non_empty_string(),
		'bar' => Type\nullable(Type\non_empty_string()),
		'baz' => Type\optional(Type\non_empty_string()),
		'other' => Type\union(Type\non_empty_string(), Type\positive_int())
	])->coerce($input);

	assertType('array{foo: non-empty-string, bar: non-empty-string|null, baz?: non-empty-string, other: int<1, max>|non-empty_string}', $output);
}


/**
 * @param mixed $input
 */
function checkNoShape($input): void
{
	$output = Type\union(Type\non_empty_string(), Type\positive_int())->coerce($input);

	assertType('int<1, max>|non-empty-string', $output);
}

/**
 * @param mixed $input
 */
function checkNoUnion($input): void
{
	$output = Type\non_empty_string()->assert($input);

	assertType('non-empty-string', $output);
}
