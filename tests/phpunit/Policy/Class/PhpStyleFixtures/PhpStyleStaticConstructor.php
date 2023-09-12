<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 NxtLvl Software Solutions.
 *
 * Freely available to use under the terms of the MIT license.
 */

namespace NxtLvlSoftware\StaticConstructors\Tests\Policy\Class\PhpStyleFixtures;

use NxtLvlSoftware\StaticConstructors\Tests\Policy\Class\PhpStyleFixtures\Traits\PhpStyleFixture;

final class PhpStyleStaticConstructor {
	use PhpStyleFixture;

	private static function __constructStatic(): void {
		self::setStaticConstructorCalled();
	}

}
