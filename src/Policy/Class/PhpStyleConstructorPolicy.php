<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 NxtLvl Software Solutions.
 *
 * Freely available to use under the terms of the MIT license.
 */

namespace NxtLvlSoftware\StaticConstructors\Policy\Class;

use ReflectionClass;
use ReflectionMethod;

/**
 * Class policy for classes which define a static constructor with
 * the `__constructStatic()` magic method.
 */
final class PhpStyleConstructorPolicy implements StaticConstructorClassPolicy {

	private const CONSTRUCTOR_METHOD_NAME = '__constructStatic';

	/**
	 * Retrieve the reflection information for a method named `__constructStatic`
	 * if it exists.
	 */
	public static function methodFor(ReflectionClass $class): ?ReflectionMethod {
		return $class->hasMethod(self::CONSTRUCTOR_METHOD_NAME) ?
			$class->getMethod(self::CONSTRUCTOR_METHOD_NAME) : null;
	}

}
