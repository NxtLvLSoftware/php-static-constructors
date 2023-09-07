<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 NxtLvl Software Solutions.
 *
 * Freely available to use under the terms of the MIT license.
 */

namespace NxtLvlSoftware\StaticConstructors\Tests\LoaderFixtures;

use NxtLvlSoftware\StaticConstructors\Tests\LoaderFixtures\Traits\LoaderTestFixture;

final class PrivateStaticConstructor {
	use LoaderTestFixture;

	private static function PrivateStaticConstructor(): void {
		self::setStaticConstructorCalled();
	}

}
