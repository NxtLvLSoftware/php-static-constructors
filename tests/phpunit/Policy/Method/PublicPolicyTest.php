<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 NxtLvl Software Solutions.
 *
 * Freely available to use under the terms of the MIT license.
 */

namespace NxtLvlSoftware\StaticConstructors\Tests\Policy\Method;

use NxtLvlSoftware\StaticConstructors\Policy\Method\Visibility\PublicVisibilityPolicy;
use NxtLvlSoftware\StaticConstructors\Tests\Policy\Method\PublicPolicyFixtures\AbstractStaticConstructor;
use NxtLvlSoftware\StaticConstructors\Tests\Policy\Method\PublicPolicyFixtures\PrivateStaticConstructor;
use NxtLvlSoftware\StaticConstructors\Tests\Policy\Method\PublicPolicyFixtures\ProtectedStaticConstructor;
use NxtLvlSoftware\StaticConstructors\Tests\Policy\Method\PublicPolicyFixtures\PublicStaticConstructor;
use NxtLvlSoftware\StaticConstructors\Tests\TestCase;
use ReflectionException;

final class PublicPolicyTest extends TestCase {

	public function setUp(): void {
		parent::setUp();

		$this->setupLoader(methodPolicies: [
			PublicVisibilityPolicy::class
		]);
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
	 * Make sure a protected constructor method fails.
	 */
	public function test_viable_protected_method_not_called() : void {
		self::assertFalse($this->hadConstructorCalled(ProtectedStaticConstructor::class));
		ProtectedStaticConstructor::noOp();
		self::assertFalse($this->hadConstructorCalled(ProtectedStaticConstructor::class));
		self::assertFalse(ProtectedStaticConstructor::hadStaticConstructorCalled());
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
				'Trying to invoke abstract method NxtLvlSoftware\StaticConstructors\Tests\Policy\PublicPolicyFixtures\AbstractStaticConstructor::AbstractStaticConstructor()',
				$e->getMessage()
			);
			self::fail('Attempted to call static constructor on abstract class'); // nothing should be invoked so being here is a failure
		}
		self::assertFalse($this->hadConstructorCalled(AbstractStaticConstructor::class));
		self::assertFalse(AbstractStaticConstructor::hadStaticConstructorCalled());
	}

}
