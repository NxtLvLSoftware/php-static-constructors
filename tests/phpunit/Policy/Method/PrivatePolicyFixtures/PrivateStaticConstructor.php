<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 NxtLvl Software Solutions.
 *
 * Freely available to use under the terms of the MIT license.
 */

namespace NxtLvlSoftware\StaticConstructors\Tests\Policy\Method\PrivatePolicyFixtures;

use NxtLvlSoftware\StaticConstructors\Tests\Policy\Method\PrivatePolicyFixtures\Traits\PrivatePolicyFixture;

class PrivateStaticConstructor {
	use PrivatePolicyFixture;

	private static function PrivateStaticConstructor(): void {
		self::setStaticConstructorCalled();
	}

}
