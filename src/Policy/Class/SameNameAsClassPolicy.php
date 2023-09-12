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
 * the same name as the class.
 */
final class SameNameAsClassPolicy implements StaticConstructorClassPolicy {

	/**
	 * Retrieve the reflection information for a method with the same
	 * name as the class if it exists.
	 */
	public static function methodFor(ReflectionClass $class): ?ReflectionMethod {
		$name = $class->getShortName();
		return $class->hasMethod($name) ?
			$class->getMethod($class->getShortName()) : null;
	}

}
