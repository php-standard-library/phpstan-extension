<?php declare(strict_types = 1);

namespace Psl\PHPStan\Option;

use Composer\InstalledVersions;
use Composer\Semver\VersionParser;
use PHPStan\Testing\TypeInferenceTestCase;
use PHPUnit\Framework\Assert;
use PHPUnit\Runner\PhptTestCase;

class PslTypeSpecifyingExtensionTest extends TypeInferenceTestCase
{

	/**
	 * @return iterable<mixed>
	 */
	public function dataFileAsserts(): iterable
	{
		yield from $this->gatherAssertTypes(__DIR__ . '/data/filter.php');
	}

	/**
	 * @dataProvider dataFileAsserts
	 * @param mixed ...$args
	 */
	public function testFileAsserts(
		string $assertType,
		string $file,
		...$args
	): void
	{
		if (!InstalledVersions::satisfies(new VersionParser(), 'azjezz/psl', '>=2.0.0')) {
			Assert::markTestSkipped(sprintf('Option component is not available in current azjezz/psl installed version'));
		}

		$this->assertFileAsserts($assertType, $file, ...$args);
	}

	/***
	 * @return string[]
	 */
	public static function getAdditionalConfigFiles(): array
	{
		return [__DIR__ . '/../../extension.neon'];
	}

}
