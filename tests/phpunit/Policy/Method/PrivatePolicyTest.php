<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 NxtLvl Software Solutions.
 *
 * Freely available to use under the terms of the MIT license.
 */

namespace NxtLvlSoftware\StaticConstructors\Tests\Policy\Method;

use NxtLvlSoftware\StaticConstructors\Policy\Method\Visibility\PrivateVisibilityPolicy;
use NxtLvlSoftware\StaticConstructors\Tests\Policy\Method\PrivatePolicyFixtures\PrivateStaticConstructor;
use NxtLvlSoftware\StaticConstructors\Tests\Policy\Method\PrivatePolicyFixtures\ProtectedStaticConstructor;
use NxtLvlSoftware\StaticConstructors\Tests\Policy\Method\PrivatePolicyFixtures\PublicStaticConstructor;
use NxtLvlSoftware\StaticConstructors\Tests\TestCase;

final class PrivatePolicyTest extends TestCase {

	public function setUp(): void {
		parent::setUp();

		$this->setupLoader(methodPolicies: [
			PrivateVisibilityPolicy::class
		]);
	}

	/**
	 * Make sure a public constructor method fails.
	 */
	public function test_viable_public_method_not_called() : void {
		self::assertFalse($this->hadConstructorCalled(PublicStaticConstructor::class));
		PublicStaticConstructor::noOp();
		self::assertFalse($this->hadConstructorCalled(PublicStaticConstructor::class));
		self::assertFalse(PublicStaticConstructor::hadStaticConstructorCalled());
	}

	/**
	 * Make sure a protected constructor method fails.
	 */
	public function test_viable_protected_method_not_called() : void {
		self::assertFalse($this->hadConstructorCalled(ProtectedStaticConstructor::class));
		ProtectedStaticConstructor::noOp();
		self::assertFalse($this->hadConstructorCalled(ProtectedStaticConstructor::class));
		self::assertFalse(ProtectedStaticConstructor::hadStaticConstructorCalled());
	}

	/**
	 * Make sure a private constructor method works.
	 */
	public function test_viable_private_method_called() : void {
		self::assertFalse($this->hadConstructorCalled(PrivateStaticConstructor::class));
		PrivateStaticConstructor::noOp();
		self::assertTrue($this->hadConstructorCalled(PrivateStaticConstructor::class));
		self::assertTrue(PrivateStaticConstructor::hadStaticConstructorCalled());
	}

}
