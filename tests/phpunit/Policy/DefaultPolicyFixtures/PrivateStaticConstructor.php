<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 NxtLvl Software Solutions.
 *
 * Freely available to use under the terms of the MIT license.
 */

namespace NxtLvlSoftware\StaticConstructors\Tests\Policy\DefaultPolicyFixtures;

use NxtLvlSoftware\StaticConstructors\Tests\Policy\DefaultPolicyFixtures\Traits\DefaultPolicyTestFixture;

final class PrivateStaticConstructor {
	use DefaultPolicyTestFixture;

	private static function PrivateStaticConstructor(): void {
		self::setStaticConstructorCalled();
	}

}
