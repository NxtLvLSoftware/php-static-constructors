<?php

/**
 * StaticConstructors.php – static-constructors
 *
 * Copyright (C) 2019 Jack Noordhuis
 *
 * @author Jack Noordhuis
 *
 * This is free and unencumbered software released into the public domain.
 *
 * Anyone is free to copy, modify, publish, use, compile, sell, or
 * distribute this software, either in source code form or as a compiled
 * binary, for any purpose, commercial or non-commercial, and by any means.
 *
 * In jurisdictions that recognize copyright laws, the author or authors
 * of this software dedicate any and all copyright interest in the
 * software to the public domain. We make this dedication for the benefit
 * of the public at large and to the detriment of our heirs and
 * successors. We intend this dedication to be an overt act of
 * relinquishment in perpetuity of all present and future rights to this
 * software under copyright law.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS BE LIABLE FOR ANY CLAIM, DAMAGES OR
 * OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
 * ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 *
 * For more information, please refer to <http://unlicense.org/>
 *
 */

declare(strict_types=1);

namespace nxtlvlsoftware\statics;

use ReflectionClass;
use ReflectionException;
use function spl_autoload_functions;
use function spl_autoload_register;
use function spl_autoload_unregister;

final class StaticConstructors {

	/** @var \nxtlvlsoftware\statics\StaticConstructors|null */
	private static $instance = null;

	public static function init() : void {
		if(static::$instance === null) {
			static::$instance = new self();
		}
	}

	/** @var \Callable */
	private $functions = [];

	/**
	 * The entry to static class constructors.
	 *
	 * This class can only exist as a singleton so we only override the existing loads
	 * if this is the first instance constructed.
	 */
	public function __construct() {
		if(static::$instance !== null) {
			return;
		}

		$this->overrideLoaders();
	}

	/**
	 * Store all the currently registered autoload functions and register this class as
	 * the primary autoloader.
	 */
	protected function overrideLoaders() : void {
		foreach(spl_autoload_functions() as $func) {
			$this->functions[] = $func;
			spl_autoload_unregister($func);
		}

		spl_autoload_register([$this, "autoload"]);
	}

	/**
	 * Look for a suitable static constructor on a class and call it.
	 *
	 * @param string $class
	 */
	protected function callConstructor(string $class) : void {
		$reflection = new ReflectionClass($class);

		try {
			$method = $reflection->getMethod($reflection->getShortName());
			if(!$method->isStatic() or $method->isAbstract() or $method->getNumberOfParameters() !== 0) {
				return;
			}

			$method->setAccessible(true);
		} catch(ReflectionException $e) {
			return; //method doesn't exist
		}

		$method->invoke(null); //invoke here to avoid catching an exception thrown by constructor
	}

	/**
	 * Our custom autoload function. We loop over the registered loaders to load the class
	 * then call the static constructor on the class if it was loaded.
	 *
	 * @param mixed ...$params
	 *
	 * @return bool
	 */
	public function autoload(...$params) : bool {
		foreach($this->functions as $func) {
			if($func(...$params)) {
				$this->callConstructor(...$params);
				return true;
			}
		}

		return false;
	}

}