<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 NxtLvl Software Solutions.
 *
 * Freely available to use under the terms of the MIT license.
 */

namespace NxtLvlSoftware\StaticConstructors\Tests;

use NxtLvlSoftware\StaticConstructors\Loader;
use function define;

final class HookTest extends TestCase {

	public function test_hook_boots_loader(): void {
		self::assertFalse(Loader::started());
		$_SERVER['DISABLE_STATIC_CONSTRUCTOR_HOOK'] = false;
		$_ENV['DISABLE_STATIC_CONSTRUCTOR_HOOK'] = false;
		require __DIR__ . '/../../src/hook.php';
		self::assertTrue(Loader::started());
	}

	public function test_server_global_disables_hook(): void {
		self::assertFalse(Loader::started());
		$_SERVER['DISABLE_STATIC_CONSTRUCTOR_HOOK'] = true;
		require __DIR__ . '/../../src/hook.php';
		self::assertFalse(Loader::started());
	}

	public function test_env_global_disables_hook(): void {
		self::assertFalse(Loader::started());
		$_ENV['DISABLE_STATIC_CONSTRUCTOR_HOOK'] = true;
		require __DIR__ . '/../../src/hook.php';
		self::assertFalse(Loader::started());
	}

	public function test_define_disables_hook(): void {
		self::assertFalse(Loader::started());
		define('DISABLE_STATIC_CONSTRUCTOR_HOOK', true);
		require __DIR__ . '/../../src/hook.php';
		self::assertFalse(Loader::started());
	}

}
