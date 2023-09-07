<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 NxtLvl Software Solutions.
 *
 * Freely available to use under the terms of the MIT license.
 */

namespace NxtLvlSoftware\StaticConstructors\Tests\Policy\Method\PublicPolicyFixtures;

use NxtLvlSoftware\StaticConstructors\Tests\Policy\Method\PublicPolicyFixtures\Traits\PublicPolicyFixture;

class ProtectedStaticConstructor {
	use PublicPolicyFixture;

	protected static function ProtectedStaticConstructor(): void {
		self::setStaticConstructorCalled();
	}

}
