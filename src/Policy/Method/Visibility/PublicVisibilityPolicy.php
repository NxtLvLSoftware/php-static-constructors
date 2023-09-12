<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 NxtLvl Software Solutions.
 *
 * Freely available to use under the terms of the MIT license.
 */

namespace NxtLvlSoftware\StaticConstructors\Policy\Method\Visibility;

/**
 * Method policy for enforcing a static constructor candidate method has public visibility.
 */
final class PublicVisibilityPolicy extends VisibilityMethodPolicy {

	protected const VISIBILITY = ConstructorVisibility::Public;

}
