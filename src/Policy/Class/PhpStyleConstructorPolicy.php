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
 * TODO: Documentation
 */
final class PhpStyleConstructorPolicy implements StaticConstructorClassPolicy {

	private const CONSTRUCTOR_METHOD_NAME = '__constructStatic';

	public static function methodFor(ReflectionClass $class): ?ReflectionMethod {
		return $class->hasMethod(self::CONSTRUCTOR_METHOD_NAME) ?
			$class->getMethod(self::CONSTRUCTOR_METHOD_NAME) : null;
	}

}
