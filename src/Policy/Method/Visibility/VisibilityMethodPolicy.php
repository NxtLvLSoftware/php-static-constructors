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
 * Abstract method policy implementation for enforcing visibility requirements on
 * static constructor methods.
 *
 * Inheriting classes should override the `VISIBILITY` constant to return a
 * value from the {@link \NxtLvlSoftware\StaticConstructors\Policy\Method\Visibility\ConstructorVisibility}
 * enum.
 */
abstract class VisibilityMethodPolicy implements StaticConstructorMethodPolicy {

	protected const VISIBILITY = ConstructorVisibility::None;

	/**
	 * Get the {@link \NxtLvlSoftware\StaticConstructors\Policy\Method\Visibility\ConstructorVisibility}
	 * enum value for this policy.
	 */
	private static function getVisibility(): ConstructorVisibility {
		return static::VISIBILITY;
	}

	/**
	 * Enforces that a method matches the visibility enforced by this policy.
	 */
	final public static function meetsRequirements(ReflectionMethod $method): bool {
		return match (self::getVisibility()) {
			ConstructorVisibility::Private   => $method->isPrivate(),
			ConstructorVisibility::Protected => $method->isProtected(),
			ConstructorVisibility::Public    => $method->isPublic(),
			ConstructorVisibility::None      => true
		};
	}

}
