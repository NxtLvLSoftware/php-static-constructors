<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 NxtLvl Software Solutions.
 *
 * Freely available to use under the terms of the MIT license.
 */

namespace NxtLvlSoftware\StaticConstructors\Tests\Policy\Class\SameNameAsClassFixtures;

use NxtLvlSoftware\StaticConstructors\Tests\Policy\Class\SameNameAsClassFixtures\Traits\SameNameAsClassFixture;

final class SameNameAsClassStaticConstructor {
	use SameNameAsClassFixture;

	private static function SameNameAsClassStaticConstructor(): void {
		self::setStaticConstructorCalled();
	}

}
