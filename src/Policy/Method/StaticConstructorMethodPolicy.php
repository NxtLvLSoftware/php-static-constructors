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
 * Interface defining requirements for static constructor method policies.
 *
 * Can be used to interact with the package and extend functionality.
 */
interface StaticConstructorMethodPolicy {

	/**
	 * Check if a static constructor candidate meets the requirements set by
	 * this policy using reflection information.
	 *
	 * @param \ReflectionMethod $method
	 *
	 * @return bool
	 */
	public static function meetsRequirements(ReflectionMethod $method): bool;

}
