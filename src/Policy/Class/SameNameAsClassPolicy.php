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

final class SameNameAsClassPolicy implements StaticConstructorClassPolicy {

	public static function methodFor(ReflectionClass $class): ?ReflectionMethod {
		$name = $class->getShortName();
		return $class->hasMethod($name) ?
			$class->getMethod($class->getShortName()) : null;
	}

}
