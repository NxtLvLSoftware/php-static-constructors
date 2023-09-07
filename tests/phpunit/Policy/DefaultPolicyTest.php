<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 NxtLvl Software Solutions.
 *
 * Freely available to use under the terms of the MIT license.
 */

namespace NxtLvlSoftware\StaticConstructors\Tests\Policy;

use NxtLvlSoftware\StaticConstructors\Tests\Policy\DefaultPolicyFixtures\ArgumentStaticConstructor;
use NxtLvlSoftware\StaticConstructors\Tests\Policy\DefaultPolicyFixtures\BothStaticConstructorsDefined;
use NxtLvlSoftware\StaticConstructors\Tests\Policy\DefaultPolicyFixtures\ChildStaticConstructor;
use NxtLvlSoftware\StaticConstructors\Tests\Policy\DefaultPolicyFixtures\ExceptionStaticConstructor;
use NxtLvlSoftware\StaticConstructors\Tests\Policy\DefaultPolicyFixtures\ParentStaticConstructor;
use NxtLvlSoftware\StaticConstructors\Tests\Policy\DefaultPolicyFixtures\PrivateStaticConstructor;
use NxtLvlSoftware\StaticConstructors\Tests\Policy\DefaultPolicyFixtures\ProtectedStaticConstructor;
use NxtLvlSoftware\StaticConstructors\Tests\Policy\DefaultPolicyFixtures\PublicStaticConstructor;
use NxtLvlSoftware\StaticConstructors\Tests\TestCase;
use Throwable;

class DefaultPolicyTest extends TestCase {

	public function setUp(): void {
		parent::setUp();

		$this->setupLoader();
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

	/**
	 * Make sure a child class does not invoke a parent constructor on itself.
	 */
	public function test_viable_child_class_method_with_parent_name_not_called() : void {
		self::assertFalse($this->hadConstructorCalled(ParentStaticConstructor::class));
		self::assertFalse($this->hadConstructorCalled(ChildStaticConstructor::class));
		ChildStaticConstructor::noOp();
		self::assertTrue($this->hadConstructorCalled(ParentStaticConstructor::class));
		self::assertFalse($this->hadConstructorCalled(ChildStaticConstructor::class));
		self::assertTrue(ParentStaticConstructor::hadStaticConstructorCalled());
		self::assertFalse(ChildStaticConstructor::hadStaticConstructorCalled());
	}

	/**
	 * Make sure exceptions aren't caught when a constructor is invoked.
	 */
	public function test_viable_method_raises_exceptions_when_thrown() : void {
		self::assertFalse($this->hadConstructorCalled(ExceptionStaticConstructor::class));
		$e = null;
		try {
			ExceptionStaticConstructor::noOp();
		} catch(Throwable $e) {

		}
		self::assertTrue($this->hadConstructorCalled(ExceptionStaticConstructor::class));
		self::assertTrue(ExceptionStaticConstructor::hadStaticConstructorCalled());
		self::assertNotNull($e);
	}

	/**
	 * Make sure a function with any arguments (even defaulted) isn't called.
	 */
	public function test_viable_method_with_arguments_not_called() : void {
		self::assertFalse($this->hadConstructorCalled(ArgumentStaticConstructor::class));
		ArgumentStaticConstructor::noOp();
		self::assertFalse($this->hadConstructorCalled(ArgumentStaticConstructor::class));
		self::assertFalse(ArgumentStaticConstructor::hadStaticConstructorCalled());
	}

	/**
	 * Make sure only one constructor is called when policy list allows multiple candidates.
	 */
	public function test_only_one_defined_constructor_called(): void {
		self::assertFalse($this->hadConstructorCalled(BothStaticConstructorsDefined::class));
		BothStaticConstructorsDefined::noOp();
		self::assertTrue($this->hadConstructorCalled(BothStaticConstructorsDefined::class));
		self::assertTrue(BothStaticConstructorsDefined::sameNameCalled());
		self::assertFalse(BothStaticConstructorsDefined::phpStyleCalled());
	}

}
