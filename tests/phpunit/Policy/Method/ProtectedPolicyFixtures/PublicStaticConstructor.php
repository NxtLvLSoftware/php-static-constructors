<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 NxtLvl Software Solutions.
 *
 * Freely available to use under the terms of the MIT license.
 */

namespace NxtLvlSoftware\StaticConstructors\Tests\Policy\Method\ProtectedPolicyFixtures;

use NxtLvlSoftware\StaticConstructors\Tests\Policy\Method\ProtectedPolicyFixtures\Traits\ProtectedPolicyFixture;

final class PublicStaticConstructor {
	use ProtectedPolicyFixture;

	public static function PublicStaticConstructor(): void {
		self::setStaticConstructorCalled();
	}

}
