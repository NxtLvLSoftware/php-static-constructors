<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 NxtLvl Software Solutions.
 *
 * Freely available to use under the terms of the MIT license.
 */

namespace NxtLvlSoftware\StaticConstructors;

use InvalidArgumentException;
use NxtLvlSoftware\StaticConstructors\Policy\Class\PhpStyleConstructorPolicy;
use NxtLvlSoftware\StaticConstructors\Policy\Class\SameNameAsClassPolicy;
use NxtLvlSoftware\StaticConstructors\Policy\Method\BaseRequirementMethodPolicy;
use NxtLvlSoftware\StaticConstructors\Policy\Method\NoArgumentsMethodPolicy;
use NxtLvlSoftware\StaticConstructors\Policy\Method\Visibility\PrivateVisibilityPolicy;
use ReflectionClass;
use function class_exists;
use function get_declared_classes;
use function spl_autoload_functions;
use function spl_autoload_register;
use function spl_autoload_unregister;

/**
 * Singleton responsible for calling static constructors on classes that
 * provide them.
 *
 * Uses {@link \NxtLvlSoftware\StaticConstructors\Policy\Class\StaticConstructorClassPolicy}
 * and {@link \NxtLvlSoftware\StaticConstructors\Policy\Method\StaticConstructorMethodPolicy}
 * to determine valid classes and methods.
 */
final class Loader {

	/**
	 * The default class policies for finding static constructor methods.
	 */
	public const DEFAULT_CLASS_POLICIES = [
		SameNameAsClassPolicy::class,
		PhpStyleConstructorPolicy::class,
	];

	/**
	 * The default method policies for determining validity of static constructor
	 * candidate methods.
	 */
	public const DEFAULT_METHOD_POLICIES = [
		NoArgumentsMethodPolicy::class,
		PrivateVisibilityPolicy::class,
	];

	private static self|null $instance = null;

	/**
	 * Check if {@link \NxtLvlSoftware\StaticConstructors\Loader::init()} has been called.
	 */
	public static function started(): bool {
		return self::$instance !== null;
	}

	/**
	 * Start the loader if an instance does not already exist.
	 *
	 * @param list<class-string<\NxtLvlSoftware\StaticConstructors\Policy\Class\StaticConstructorClassPolicy>> $classPolicies Policy class names for determining if a class has a valid static constructor method defined.
	 * @param list<class-string<\NxtLvlSoftware\StaticConstructors\Policy\Method\StaticConstructorMethodPolicy>> $methodPolicies Policy class names for determining validity of candidate static constructor methods.
	 * @param bool $checkLoadedClasses Should the loader check for a static constructor on classes that are already declared in the runtime?
	 */
	public static function init(
		array $classPolicies = self::DEFAULT_CLASS_POLICIES,
		array $methodPolicies = self::DEFAULT_METHOD_POLICIES,
		bool $checkLoadedClasses = true
	): void {
		if (self::started()) {
			return;
		}

		new self($classPolicies, $methodPolicies, $checkLoadedClasses);
	}

	/** @var list<callable(class-string): void> */
	private array $proxiedLoaders = [];

	/**
	 * The entry to static class constructors.
	 *
	 * This class can only exist as a singleton so that we only override
	 * the existing autoloader's if this is the first instance constructed.
	 *
	 * @param list<class-string<\NxtLvlSoftware\StaticConstructors\Policy\Class\StaticConstructorClassPolicy>> $classPolicies
	 * @param list<class-string<\NxtLvlSoftware\StaticConstructors\Policy\Method\StaticConstructorMethodPolicy>> $methodPolicies
	 */
	private function __construct(
		private readonly array $classPolicies,
		private readonly array $methodPolicies,
		bool $checkLoadedClasses
	) {
		$policyClasses = [
			...$classPolicies,
			BaseRequirementMethodPolicy::class,
			...$this->methodPolicies
		]; // force load policy classes
		foreach ($policyClasses as $class) {
			if (!class_exists($class)) {
				throw new InvalidArgumentException('Provided policy class that could not be found. Class: ' . $class);
			}
		}

		self::$instance = $this;

		$this->overrideSplLoaders();
		if ($checkLoadedClasses) {
			$this->checkLoadedClasses();
		}
	}

	/**
	 * Our custom autoload function. We loop over the registered loaders
	 * to load the class then call the static constructor on the class
	 * if it was loaded.
	 *
	 * @param class-string $className
	 */
	public function autoload(string $className): void {
		foreach ($this->proxiedLoaders as $func) {
			$func($className);
			if (class_exists($className, false)) {
				break;
			}
		}

		$this->callStaticConstructor($className);
	}

	/**
	 * Look for a suitable static constructor on a class and call it.
	 *
	 * @param class-string $className
	 */
	private function callStaticConstructor(string $className): void {
		/** @var \ReflectionMethod|null $method */
		$method = null;
		$reflection = new ReflectionClass($className);
		foreach ($this->classPolicies as $classPolicy) {
			$method = $classPolicy::methodFor($reflection);
			if ($method === null || !(BaseRequirementMethodPolicy::meetsRequirements($method))) {
				$method = null;
				continue;
			}

			foreach ($this->methodPolicies as $methodPolicy) {
				if (!($methodPolicy::meetsRequirements($method))) {
					$method = null;
					continue 2;
				}
			}
			break; // valid
		}
		if ($method === null) {
			return;
		}

		$method->invoke(null);
	}

	/**
	 * Store all the currently registered autoload functions and register
	 * this class as the primary autoloader.
	 */
	private function overrideSplLoaders(): void {
		foreach (spl_autoload_functions() as $func) {
			$this->proxiedLoaders[] = $func;
			spl_autoload_unregister($func);
		}

		spl_autoload_register(
			function (string $className) {
				/** @var class-string $className */
				$this->autoload($className);
			},
			true,
			true
		);
	}

	/**
	 * Check classes already declared in the runtime for static constructor methods
	 * and call them.
	 */
	private function checkLoadedClasses(): void {
		foreach (get_declared_classes() as $className) {
			$this->callStaticConstructor($className);
		}
	}

}
