<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 NxtLvl Software Solutions.
 *
 * Freely available to use under the terms of the MIT license.
 */

namespace NxtLvlSoftware\StaticConstructors\Tests\Policy\DefaultPolicyFixtures;

use BadMethodCallException;
use NxtLvlSoftware\StaticConstructors\Tests\Policy\DefaultPolicyFixtures\Traits\DefaultPolicyTestFixture;

class ExceptionStaticConstructor {
	use DefaultPolicyTestFixture;

	/**
	 * @return never-return
	 *
	 * @throws \BadMethodCallException
	 */
	private static function ExceptionStaticConstructor(): void {
		self::setStaticConstructorCalled();
		throw new BadMethodCallException();
	}

}
