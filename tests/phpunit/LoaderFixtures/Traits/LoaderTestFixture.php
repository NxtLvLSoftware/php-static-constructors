<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 NxtLvl Software Solutions.
 *
 * Freely available to use under the terms of the MIT license.
 */

namespace NxtLvlSoftware\StaticConstructors\Tests\LoaderFixtures\Traits;

use NxtLvlSoftware\StaticConstructors\Tests\LoaderTest;
use NxtLvlSoftware\StaticConstructors\Tests\TestFixture;

trait LoaderTestFixture {
	use TestFixture;

	/**
	 * @return class-string<\NxtLvlSoftware\StaticConstructors\Tests\TestCase>
	 */
	protected static function getTestCase(): string {
		return LoaderTest::class;
	}

}
