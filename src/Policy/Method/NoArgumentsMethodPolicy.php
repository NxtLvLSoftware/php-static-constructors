<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 NxtLvl Software Solutions.
 *
 * Freely available to use under the terms of the MIT license.
 */

namespace NxtLvlSoftware\StaticConstructors\Policy\Method;

use ReflectionMethod;

final class NoArgumentsMethodPolicy implements StaticConstructorMethodPolicy {

	public static function meetsRequirements(ReflectionMethod $method): bool {
		return $method->getNumberOfParameters() === 0;
	}

}
