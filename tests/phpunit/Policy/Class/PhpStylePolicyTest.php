<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 NxtLvl Software Solutions.
 *
 * Freely available to use under the terms of the MIT license.
 */

namespace NxtLvlSoftware\StaticConstructors\Tests\Policy\Class;

use NxtLvlSoftware\StaticConstructors\Policy\Class\PhpStyleConstructorPolicy;
use NxtLvlSoftware\StaticConstructors\Tests\Policy\Class\PhpStyleFixtures\BothStaticConstructorsDefined;
use NxtLvlSoftware\StaticConstructors\Tests\Policy\Class\PhpStyleFixtures\PhpStyleStaticConstructor;
use NxtLvlSoftware\StaticConstructors\Tests\TestCase;

final class PhpStylePolicyTest extends TestCase {

	public function setUp(): void {
		parent::setUp();

		$this->setupLoader([
			PhpStyleConstructorPolicy::class
		]);
	}

	public function test_constructor_called(): void {
		self::assertFalse($this->hadConstructorCalled(PhpStyleStaticConstructor::class));
		PhpStyleStaticConstructor::noOp();
		self::assertTrue($this->hadConstructorCalled(PhpStyleStaticConstructor::class));
		self::assertTrue(PhpStyleStaticConstructor::hadStaticConstructorCalled());
	}

	public function test_only_php_style_called_when_both_defined(): void {
		self::assertFalse($this->hadConstructorCalled(BothStaticConstructorsDefined::class));
		BothStaticConstructorsDefined::noOp();
		self::assertTrue($this->hadConstructorCalled(BothStaticConstructorsDefined::class));
		self::assertTrue(BothStaticConstructorsDefined::hadStaticConstructorCalled());
		self::assertTrue(BothStaticConstructorsDefined::phpStyleCalled());
		self::assertFalse(BothStaticConstructorsDefined::sameNameCalled());
	}

}
