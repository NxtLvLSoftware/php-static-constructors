<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 NxtLvl Software Solutions.
 *
 * Freely available to use under the terms of the MIT license.
 */

namespace NxtLvlSoftware\StaticConstructors\Tests;

use InvalidArgumentException;
use NxtLvlSoftware\StaticConstructors\Loader;
use NxtLvlSoftware\StaticConstructors\Tests\Policy\DefaultPolicyFixtures\PrivateStaticConstructor;
use ReflectionClass;
use ReflectionException;

final class LoaderTest extends TestCase {

	private static function getLoaderInstance(): ?Loader {
		try {
			$class = (new ReflectionClass(Loader::class));
			return $class->getProperty('instance')->getValue();
		} catch (ReflectionException $e) {
			// shouldn't happen
		}
		return null;
	}

	public function test_only_one_instance_allowed(): void {
		self::assertFalse(Loader::started());
		self::assertNull(self::getLoaderInstance());
		Loader::init(checkLoadedClasses: false);
		self::assertTrue(Loader::started());

		$instance = self::getLoaderInstance();
		self::assertNotNull($instance);

		Loader::init(checkLoadedClasses: false);
		self::assertEquals($instance, self::getLoaderInstance());
	}

	public function test_loads_declared_classes(): void {
		self::assertFalse($this->hadConstructorCalled(PrivateStaticConstructor::class));
		PrivateStaticConstructor::noOp();
		self::assertFalse($this->hadConstructorCalled(PrivateStaticConstructor::class));
		self::assertFalse(PrivateStaticConstructor::hadStaticConstructorCalled());

		Loader::init(checkLoadedClasses: true);
		self::assertTrue($this->hadConstructorCalled(PrivateStaticConstructor::class));
		self::assertTrue(PrivateStaticConstructor::hadStaticConstructorCalled());
	}

	public function test_skips_calling_declared_classes(): void {
		self::assertFalse($this->hadConstructorCalled(PrivateStaticConstructor::class));
		PrivateStaticConstructor::noOp();
		self::assertFalse($this->hadConstructorCalled(PrivateStaticConstructor::class));
		self::assertFalse(PrivateStaticConstructor::hadStaticConstructorCalled());

		Loader::init(checkLoadedClasses: false);
		self::assertFalse($this->hadConstructorCalled(PrivateStaticConstructor::class));
		self::assertFalse(PrivateStaticConstructor::hadStaticConstructorCalled());
	}

	public function test_throws_on_invalid_policy(): void {
		self::assertFalse(Loader::started());
		self::assertNull(self::getLoaderInstance());
		$error = null;
		try {
			Loader::init(
				[
					'I\DONT_EXIST'
				],
				checkLoadedClasses: false
			);
		} catch(InvalidArgumentException $error) {}
		self::assertNotNull($error);
		self::assertEquals(
			'Provided policy class that could not be found. Class: I\DONT_EXIST',
			$error->getMessage()
		);
	}

}
