<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 NxtLvl Software Solutions.
 *
 * Freely available to use under the terms of the MIT license.
 */

namespace NxtLvlSoftware\StaticConstructors\Tests\Policy\Method\ProtectedPolicyFixtures;

use NxtLvlSoftware\StaticConstructors\Tests\Policy\Method\ProtectedPolicyFixtures\Traits\ProtectedPolicyFixture;

abstract class AbstractStaticConstructor {
	use ProtectedPolicyFixture;

	abstract protected static function AbstractStaticConstructor(): void;

}
