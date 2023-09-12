<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 NxtLvl Software Solutions.
 *
 * Freely available to use under the terms of the MIT license.
 */

namespace NxtLvlSoftware\StaticConstructors\Tests;

trait TestFixture {

	/**
	 * @return class-string<\NxtLvlSoftware\StaticConstructors\Tests\TestCase>
	 */
	abstract protected static function getTestCase(): string;

	private static bool $constructed = false;

	public static function hadStaticConstructorCalled(): bool {
		return self::$constructed;
	}

	private static function setStaticConstructorCalled(): void {
		self::$constructed = true;
		self::getTestCase()::markConstructorCalled(static::class);
	}

	public static function noOp(): void {}

	public static function reset(): void {
		self::$constructed = false;
	}

}
