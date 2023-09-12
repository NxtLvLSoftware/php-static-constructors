<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 NxtLvl Software Solutions.
 *
 * Freely available to use under the terms of the MIT license.
 */

namespace NxtLvlSoftware\StaticConstructors\Policy\Method;

use ReflectionMethod;

/**
 * Method policy for enforcing a static constructor candidate method takes no arguments (even defaulted.)
 */
final class NoArgumentsMethodPolicy implements StaticConstructorMethodPolicy {

	/**
	 * Enforces a method has no parameters.
	 */
	public static function meetsRequirements(ReflectionMethod $method): bool {
		return $method->getNumberOfParameters() === 0;
	}

}
