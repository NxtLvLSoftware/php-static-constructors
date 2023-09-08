<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 NxtLvl Software Solutions.
 *
 * Freely available to use under the terms of the MIT license.
 */

namespace NxtLvlSoftware\StaticConstructors\Policy\Method\Visibility;

use NxtLvlSoftware\StaticConstructors\Policy\Method\StaticConstructorMethodPolicy;
use ReflectionMethod;

/**
 * TODO: Documentation
 */
abstract class VisibilityMethodPolicy implements StaticConstructorMethodPolicy {

	protected const VISIBILITY = ConstructorVisibility::None;

	private static function getVisibility(): ConstructorVisibility {
		return static::VISIBILITY;
	}

	final public static function meetsRequirements(ReflectionMethod $method): bool {
		return match (self::getVisibility()) {
			ConstructorVisibility::Private   => $method->isPrivate(),
			ConstructorVisibility::Protected => $method->isProtected(),
			ConstructorVisibility::Public    => $method->isPublic(),
			ConstructorVisibility::None      => true
		};
	}

}
