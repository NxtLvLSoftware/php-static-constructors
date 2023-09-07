<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 NxtLvl Software Solutions.
 *
 * Freely available to use under the terms of the MIT license.
 */

namespace NxtLvlSoftware\StaticConstructors\Tests\Policy\Class;

use NxtLvlSoftware\StaticConstructors\Policy\Class\SameNameAsClassPolicy;
use NxtLvlSoftware\StaticConstructors\Tests\Policy\Class\SameNameAsClassFixtures\BothStaticConstructorsDefined;
use NxtLvlSoftware\StaticConstructors\Tests\Policy\Class\SameNameAsClassFixtures\SameNameAsClassStaticConstructor;
use NxtLvlSoftware\StaticConstructors\Tests\TestCase;

final class SameNameAsClassPolicyTest extends TestCase {

	public function setUp(): void {
		parent::setUp();

		$this->setupLoader([
			SameNameAsClassPolicy::class
		]);
	}

	public function test_constructor_called(): void {
		self::assertFalse($this->hadConstructorCalled(SameNameAsClassStaticConstructor::class));
		SameNameAsClassStaticConstructor::noOp();
		self::assertTrue($this->hadConstructorCalled(SameNameAsClassStaticConstructor::class));
		self::assertTrue(SameNameAsClassStaticConstructor::hadStaticConstructorCalled());
	}

	public function test_only_same_name_as_class_called_when_both_defined(): void {
		self::assertFalse($this->hadConstructorCalled(BothStaticConstructorsDefined::class));
		BothStaticConstructorsDefined::noOp();
		self::assertTrue($this->hadConstructorCalled(BothStaticConstructorsDefined::class));
		self::assertTrue(BothStaticConstructorsDefined::hadStaticConstructorCalled());
		self::assertFalse(BothStaticConstructorsDefined::phpStyleCalled());
		self::assertTrue(BothStaticConstructorsDefined::sameNameCalled());
	}

}
