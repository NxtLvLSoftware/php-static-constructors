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
 * Interface defining requirements for static constructor class policies.
 *
 * Can be used to interact with the package and extend functionality.
 */
interface StaticConstructorClassPolicy {

	/**
	 * Retrieve the static constructor reflection information for the
	 * provided class if it exists.
	 *
	 * @param \ReflectionClass<object> $class
	 *
	 * @return \ReflectionMethod|null
	 */
	public static function methodFor(ReflectionClass $class): ?ReflectionMethod;

}
