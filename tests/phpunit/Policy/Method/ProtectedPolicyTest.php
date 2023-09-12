<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 NxtLvl Software Solutions.
 *
 * Freely available to use under the terms of the MIT license.
 */

namespace NxtLvlSoftware\StaticConstructors\Tests\Policy\Method;

use NxtLvlSoftware\StaticConstructors\Policy\Method\Visibility\ProtectedVisibilityPolicy;
use NxtLvlSoftware\StaticConstructors\Tests\Policy\Method\ProtectedPolicyFixtures\AbstractStaticConstructor;
use NxtLvlSoftware\StaticConstructors\Tests\Policy\Method\ProtectedPolicyFixtures\PrivateStaticConstructor;
use NxtLvlSoftware\StaticConstructors\Tests\Policy\Method\ProtectedPolicyFixtures\ProtectedStaticConstructor;
use NxtLvlSoftware\StaticConstructors\Tests\Policy\Method\ProtectedPolicyFixtures\PublicStaticConstructor;
use NxtLvlSoftware\StaticConstructors\Tests\TestCase;
use ReflectionException;

final class ProtectedPolicyTest extends TestCase {

	public function setUp(): void {
		parent::setUp();

		$this->setupLoader(methodPolicies: [
			ProtectedVisibilityPolicy::class
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
	 * Make sure a protected constructor method works.
	 */
	public function test_viable_protected_method_called() : void {
		self::assertFalse($this->hadConstructorCalled(ProtectedStaticConstructor::class));
		ProtectedStaticConstructor::noOp();
		self::assertTrue($this->hadConstructorCalled(ProtectedStaticConstructor::class));
		self::assertTrue(ProtectedStaticConstructor::hadStaticConstructorCalled());
	}

	/**
	 * Make sure a private constructor method fails.
	 */
	public function test_viable_private_method_not_called() : void {
		self::assertFalse($this->hadConstructorCalled(PrivateStaticConstructor::class));
		PrivateStaticConstructor::noOp();
		self::assertFalse($this->hadConstructorCalled(PrivateStaticConstructor::class));
		self::assertFalse(PrivateStaticConstructor::hadStaticConstructorCalled());
	}

	/**
	 * Make sure suitable abstract constructors aren't called.
	 */
	public function test_viable_abstract_method_not_called() : void {
		self::assertFalse($this->hadConstructorCalled(AbstractStaticConstructor::class));
		try {
			AbstractStaticConstructor::noOp();
		} catch(ReflectionException $e) {
			self::assertEquals(
				'Trying to invoke abstract method NxtLvlSoftware\StaticConstructors\Tests\Policy\ProtectedPolicyFixtures\AbstractStaticConstructor::AbstractStaticConstructor()',
				$e->getMessage()
			);
			self::fail('Attempted to call static constructor on abstract class'); // nothing should be invoked so being here is a failure
		}
		self::assertFalse($this->hadConstructorCalled(AbstractStaticConstructor::class));
		self::assertFalse(AbstractStaticConstructor::hadStaticConstructorCalled());
	}

}
