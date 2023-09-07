<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 NxtLvl Software Solutions.
 *
 * Freely available to use under the terms of the MIT license.
 */

$hook = static function(string $key = 'DISABLE_STATIC_CONSTRUCTOR_HOOK'): void {
	$disabled = $_SERVER[$key] ?? $_ENV[$key] ?? defined($key);
	if(!$disabled) {
		\NxtLvlSoftware\StaticConstructors\Loader::init();
	}
};

$hook();
unset($hook);
