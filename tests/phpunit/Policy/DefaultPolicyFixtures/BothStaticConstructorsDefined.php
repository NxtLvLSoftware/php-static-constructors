<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 NxtLvl Software Solutions.
 *
 * Freely available to use under the terms of the MIT license.
 */

namespace NxtLvlSoftware\StaticConstructors\Tests\Policy\DefaultPolicyFixtures;

use NxtLvlSoftware\StaticConstructors\Tests\Policy\DefaultPolicyFixtures\Traits\DefaultPolicyTestFixture;

final class BothStaticConstructorsDefined {
	use DefaultPolicyTestFixture;

	private static bool $phpStyleCalled = false;
	private static bool $sameNameCalled = false;

	private static function __constructStatic(): void {
		self::$phpStyleCalled = true;
		self::setStaticConstructorCalled();
	}

	private static function BothStaticConstructorsDefined(): void {
		self::$sameNameCalled = true;
		self::setStaticConstructorCalled();
	}

	public static function phpStyleCalled() : bool {
		return self::$phpStyleCalled;
	}

	public static function sameNameCalled() : bool {
		return self::$sameNameCalled;
	}


}
