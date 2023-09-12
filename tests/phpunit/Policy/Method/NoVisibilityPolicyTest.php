<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 NxtLvl Software Solutions.
 *
 * Freely available to use under the terms of the MIT license.
 */

namespace NxtLvlSoftware\StaticConstructors\Tests\Policy\Method;

use NxtLvlSoftware\StaticConstructors\Tests\Policy\Method\NoVisibilityFixtures\PrivateStaticConstructor;
use NxtLvlSoftware\StaticConstructors\Tests\Policy\Method\NoVisibilityFixtures\ProtectedStaticConstructor;
use NxtLvlSoftware\StaticConstructors\Tests\Policy\Method\NoVisibilityFixtures\PublicStaticConstructor;
use NxtLvlSoftware\StaticConstructors\Tests\TestCase;

final class NoVisibilityPolicyTest extends TestCase {

	public function setUp(): void {
		parent::setUp();

		$this->setupLoader(methodPolicies: []);
	}

	/**
	 * Make sure a public constructor method works.
	 */
	public function test_viable_public_method_called() : void {
		self::assertFalse($this->hadConstructorCalled(PublicStaticConstructor::class));
		PublicStaticConstructor::noOp();
		self::assertTrue($this->hadConstructorCalled(PublicStaticConstructor::class));
		self::assertTrue(PublicStaticConstructor::hadStaticConstructorCalled());
	}

	/**
	 * Make sure a protected constructor method works.
	 */
	public function test_viable_protected_method_called() : void {
		self::assertFalse($this->hadConstructorCalled(ProtectedStaticConstructor::class));
		ProtectedStaticConstructor::noOp();
		self::assertTrue($this->hadConstructorCalled(ProtectedStaticConstructor::class));
		self::assertTrue(ProtectedStaticConstructor::hadStaticConstructorCalled());
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
