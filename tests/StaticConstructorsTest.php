<?php

/**
 * StaticConstructorsTest.php – static-constructors
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

namespace nxtlvlsoftware\tests\statics;

use nxtlvlsoftware\tests\statics\fixtures\AbstractStaticConstructor;
use nxtlvlsoftware\tests\statics\fixtures\ArgumentStaticConstructor;
use nxtlvlsoftware\tests\statics\fixtures\ChildStaticConstructor;
use nxtlvlsoftware\tests\statics\fixtures\ExceptionStaticConstructor;
use nxtlvlsoftware\tests\statics\fixtures\PrivateStaticConstructor;
use nxtlvlsoftware\tests\statics\fixtures\ProtectedStaticConstructor;
use nxtlvlsoftware\tests\statics\fixtures\PublicStaticConstructor;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class StaticConstructorsTest extends TestCase {

	/** @var bool */
	public static $passed = false;

	public function setUp() : void {
		self::$passed = false;
	}

	/**
	 * Make sure a public constructor method works.
	 */
	public function testPublicConstructor() : void {
		$this->assertFalse(self::$passed);
		new PublicStaticConstructor;
		$this->assertTrue(self::$passed);
	}

	/**
	 * Make sure a protected constructor method works.
	 */
	public function testProtectedConstructor() : void {
		$this->assertFalse(self::$passed);
		new ProtectedStaticConstructor;
		$this->assertTrue(self::$passed);
	}

	/**
	 * Make sure a private constructor method works.
	 */
	public function testPrivateConstructor() : void {
		$this->assertFalse(self::$passed);
		new PrivateStaticConstructor;
		$this->assertTrue(self::$passed);
	}

	/**
	 * Make sure a child class does not invoke a parent constructor on itself.
	 */
	public function testInheritedConstructor() : void {
		$this->assertFalse(self::$passed);
		new ChildStaticConstructor;
		$this->assertTrue(self::$passed);
		$this->assertFalse(ChildStaticConstructor::$called);
	}

	/**
	 * Make sure suitable abstract constructors aren't called.
	 */
	public function testAbstractConstructor() : void {
		$this->assertFalse(self::$passed);
		try {
			AbstractStaticConstructor::dummy();
		} catch(ReflectionException $e) {
			$this->assertEquals("Trying to invoke abstract method nxtlvlsoftware\\tests\statics\\fixtures\AbstractStaticConstructor::AbstractStaticConstructor()", $e->getMessage());
		}
		$this->assertFalse(self::$passed);
	}

	/**
	 * Make sure exceptions aren't caught when a constructor is invoked.
	 */
	public function testExceptionNotCaught() : void {
		$e = null;
		try {
			new ExceptionStaticConstructor;
		} catch(ReflectionException $e) {

		}
		$this->assertNotNull($e);
	}

	public function testArgumentConstructorNotCalled() : void {
		$this->assertFalse(self::$passed);
		new ArgumentStaticConstructor;
		$this->assertFalse(self::$passed);
	}

}