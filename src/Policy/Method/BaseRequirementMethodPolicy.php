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
 * Base requirments that must be enforced on any method for it to be
 * considered a static constructor.
 */
final class BaseRequirementMethodPolicy implements StaticConstructorMethodPolicy {

	public static function meetsRequirements(ReflectionMethod $method): bool {
		return $method->isStatic() && !$method->isAbstract() && $method->isUserDefined();
	}

}
