<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 NxtLvl Software Solutions.
 *
 * Freely available to use under the terms of the MIT license.
 */

namespace NxtLvlSoftware\StaticConstructors\Tests\Policy\DefaultPolicyFixtures;

use NxtLvlSoftware\StaticConstructors\Tests\Policy\DefaultPolicyFixtures\Traits\DefaultPolicyTestFixture;

class ProtectedStaticConstructor {
	use DefaultPolicyTestFixture;

	protected static function ProtectedStaticConstructor(): void {
		self::setStaticConstructorCalled();
	}

}
