<?php declare(strict_types = 1);

namespace Psl\PHPStan\Type;

use Composer\InstalledVersions;
use Composer\Semver\VersionParser;
use PHPStan\Testing\TypeInferenceTestCase;
use Psl\Type\Internal\NullishType;
use function class_exists;

class PslTypeSpecifyingExtensionTest extends TypeInferenceTestCase
{

	/**
	 * @return iterable<mixed>
	 */
	public function dataFileAsserts(): iterable
	{
		yield from $this->gatherAssertTypes(__DIR__ . '/data/coerce.php');
		yield from $this->gatherAssertTypes(__DIR__ . '/data/assert.php');
		yield from $this->gatherAssertTypes(__DIR__ . '/data/matches.php');
		if (class_exists(NullishType::class)) {
			yield from $this->gatherAssertTypes(__DIR__ . '/data/nullishCoerce.php');
			yield from $this->gatherAssertTypes(__DIR__ . '/data/nullishAssert.php');
			yield from $this->gatherAssertTypes(__DIR__ . '/data/nullishMatches.php');
		}
		if (InstalledVersions::satisfies(new VersionParser(), 'php-standard-library/php-standard-library', '<2.0.0')) {
			yield from $this->gatherAssertTypes(__DIR__ . '/data/complexTypev1.php');
		} else {
			yield from $this->gatherAssertTypes(__DIR__ . '/data/complexTypev2.php');
		}
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
