<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 NxtLvl Software Solutions.
 *
 * Freely available to use under the terms of the MIT license.
 */

namespace NxtLvlSoftware\StaticConstructors\Policy\Method\Visibility;

/**
 * Specifies valid static constructor visibility levels as an enum.
 *
 * {@see \NxtLvlSoftware\StaticConstructors\Policy\Method\Visibility\VisibilityMethodPolicy}
 */
enum ConstructorVisibility {

	case Public;
	case Protected;
	case Private;
	case None;

}
