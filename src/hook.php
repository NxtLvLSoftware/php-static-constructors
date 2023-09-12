<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 NxtLvl Software Solutions.
 *
 * Freely available to use under the terms of the MIT license.
 */

/**
 * Temporary callable for bootstrapping the static constructor loader.
 *
 * This is unset after calling to ensure it isn't available after use as
 * this file is automatically included by the composer autoloader.
 */
$hook = static function(string $key = 'DISABLE_STATIC_CONSTRUCTOR_HOOK'): void {
	$disabled = $_SERVER[$key] ?? $_ENV[$key] ?? defined($key);
	if(!$disabled) {
		\NxtLvlSoftware\StaticConstructors\Loader::init();
	}
};

$hook(); // call hook function
unset($hook); // remove hook function from runtime
