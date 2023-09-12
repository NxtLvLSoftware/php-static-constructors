<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 NxtLvl Software Solutions.
 *
 * Freely available to use under the terms of the MIT license.
 */

namespace NxtLvlSoftware\StaticConstructors\Tests;

use NxtLvlSoftware\StaticConstructors\Loader;
use PHPUnit\Framework\TestCase as PhpUnitTestCase;

abstract class TestCase extends PhpUnitTestCase {

	/** @var array<class-string, bool>  */
	protected static array $constructorsCalled = [];

	/**
	 * @param class-string $className
	 */
	public static function markConstructorCalled(string $className): void {
		static::$constructorsCalled[$className] = true;
	}

	/**
	 * @param class-string $className
	 */
	protected function hadConstructorCalled(string $className): bool {
		return static::$constructorsCalled[$className] ?? false;
	}

	public function setUp(): void {
		static::$constructorsCalled = [];
	}

	/**
	 * @param list<class-string<\NxtLvlSoftware\StaticConstructors\Policy\Class\StaticConstructorClassPolicy>> $classPolicies
	 * @param list<class-string<\NxtLvlSoftware\StaticConstructors\Policy\Method\StaticConstructorMethodPolicy>> $methodPolicies
	 */
	public function setupLoader(
		array $classPolicies = Loader::DEFAULT_CLASS_POLICIES,
		array $methodPolicies = Loader::DEFAULT_METHOD_POLICIES
	): void {
		if (Loader::started()) {
			static::fail('Loader is already started, is the hook functionality disabled?');
		}

		Loader::init($classPolicies, $methodPolicies, false);
	}

}
