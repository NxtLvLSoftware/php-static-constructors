<p align="center">
  <a href="https://nxtlvlsoftware.github.io/php-static-constructors/"><picture>
    <source media="(prefers-color-scheme: dark)" srcset="https://raw.githubusercontent.com/NxtLvLSoftware/php-static-constructors/dist/.github/banner-dark.svg">
    <source media="(prefers-color-scheme: light)" srcset="https://raw.githubusercontent.com/NxtLvLSoftware/php-static-constructors/dist/.github/banner-light.svg">
    <img alt="Project Banner (nxtlvlsoftware/static-constructors)" src="https://raw.githubusercontent.com/NxtLvLSoftware/php-static-constructors/dist/.github/banner-light.svg" width="350" height="160" style="max-width: 100%;">
  </picture></a>
</p>

<h1 align="center">
  Static Constructors for PHP
</h1>

<h4 align="center" style="font-style: italic;">
  Brings static class initialization to PHP!
</h4>

<p align="center">
    <a href="https://github.com/NxtLvlSoftware/php-static-constructors/actions"><img src="https://img.shields.io/github/actions/workflow/status/NxtLvlSoftware/php-static-constructors/ci.yml?branch=dev" alt="Build Status"></a>
    <a href="https://nxtlvlsoftware.github.io/php-static-constructors/coverage/"><img src="https://nxtlvlsoftware.github.io/php-static-constructors/coverage/badge.svg" alt="Coverage Status"></a>
    <a href="https://packagist.org/packages/nxtlvlsoftware/static-constructors"><img src="https://img.shields.io/packagist/dt/NxtLvlSoftware/static-constructors.svg" alt="Total Downloads"></a>
    <a href="https://github.com/NxtLvlSoftware/php-static-constructors/releases"><img src="https://img.shields.io/packagist/v/NxtLvlSoftware/static-constructors.svg" alt="Latest Release"></a>
    <a href="https://github.com/NxtLvlSoftware/php-static-constructors/blob/dev/LICENSE"><img src="https://img.shields.io/packagist/l/NxtLvlSoftware/static-constructors" alt="License"></a>
</p>

<br>
<hr>
<br>

* [Documentation](https://nxtlvlsoftware.github.io/php-static-constructors/docs/)
* [Coverage Report](https://nxtlvlsoftware.github.io/php-static-constructors/coverage/)
* [About](#about)
* [Installation](#installation)
* [Under The Hood](#under-the-hood)
* [More Information](#more-information)
* [Contributing](#contributing)
  * [Issues](#issues)
* [License](#license-information)

<br>
<hr>
<br>

### About

This package brings a feature present in most other popular programming languages
to PHP, static constructors. This is implemented using policies which search
classes for a suitable static method. The default policies look for private methods
with the same name as the class (case-sensitive) or a PHP-style magic `__constructStatic()`.
The next steps are making sure the method accepts no arguments, is not abstract and is
user defined (don't want to accidentally call something from an extension!)

Here's a quick singleton example:
```php
// src/Example.php
class Example {

    private static self $instance = null;
    
    public static function get(): self {
        return self::$instance;
    }

    private static function Example(): void {
        self::$instance = new self();
    }

    public function echo(): void {
        echo "Hello World!" . PHP_EOL;
    }

}

// src/bootstrap.php
require_once __DIR__ . '/../vendor/autoload.php';
Example::get()->echo();
```

You can probably already guess the output of this code, the console will output
`Hello World!`. The first time the class or any child classes are referenced in
your code the static constructor will be called. Parent constructors will always
be called first due to the way PHP loads classes:
```php
abstract class Parent {
    private static function __constuctStatic() { echo "Called first" . PHP_EOL; }
}

class Child extends Parent {
    private static function __constructStatic() { echo "Not called" . PHP_EOL; }
    private static function Child() { echo "Called second" . PHP_EOL; }
}


// src/bootstrap.php
require_once __DIR__ . '/../vendor/autoload.php';
$object = new Child;
```

Method name and visibility requirments can be customised through the use of policies.
It is not recommended to do this for packages as it introduces incompatibility issues
when other code relies on default behaviours. This feature is best put to use in
frameworks where one of the project goals is to provide a custom, well documented
environment.

### Installation

Install with composer on the command line:

```bash
$ composer require nxtlvlsoftware/static-constructors
```

Or add the dependency directly to your composer.json manifest:

```json
{
  "require": {
    "nxtlvlsoftware/static-constructors": "^1.0.0"
  }
}
```

Composer will automatically handle 'hooking' the library for you when you require
your `vendor/autoload.php` file. If you use another autoloader in your project you
can disable the automatic hook by adding `define('DISABLE_STATIC_CONSTRUCTOR_HOOK', true)`
anywhere before you `include` the composer autoload file. You must then hook the
library yourself with `\NxtlvlSoftware\StaticConstructors\Loader::init()`. The
`$_SERVER['DISABLE_STATIC_CONSTRUCTOR_HOOK']` and `$_ENV['DISABLE_STATIC_CONSTRUCTOR_HOOK']`
globals are checked for a `true` value as well.

When manually initializing the loader
you can choose which policies to enable for the method name resolution and method
requirments. It is also possible to control whether the loader should search classes
which are already declared in the runtime.
```php
use \NxtlvlSoftware\StaticConstructors\Loader;

Loader::init(
    // same as default
    classPolicies: Loader::DEFAULT_CLASS_POLICIES,
    // don't check if method is public/protected/private
    methodPolicies: [],
    // only check classes that the runtime loads after init call
    checkLoadedClasses: false
);
```

### Under the hood

This is all implemented by registering our own class loader and unregistering any
previously registered loaders. When our loader is called we do the job PHP normally
does, loop over all the actual class loaders to try and load the class but this is where
we perform the magic that allows us to use static constructors. If a class is loaded by
one of the real loaders, we look for a suitable static constructor method on the loaded
class using reflection. If we find a constructor, we simply call it! PHP does most of the
dirty work for us. If a class extends another then the parent class is automatically
loaded by PHP before the child, so we don't even have to walk up the inheritance chain
ourselves. Since PHP only uses autoloader's to load a class into the runtime once, the
constructors are only ever called the first time the class is referenced.

### More Information
A good summary of why you would want to use static constructors is available [in this blog post](https://liamhammett.com/static-constructors-in-php-y0zPVbQl)
by [@ImLiam](https://github.com/ImLiam). The article is based on [another](https://github.com/vladimmi/construct-static),
older package which inspired the `__constructStatic()` magic method in this package.

## Contributing

#### Issues

Found a problem with this project? Make sure to open an issue on the [issue tracker](https://github.com/NxtLvLSoftware/php-static-constructors/issues)
and we'll do our best to get it sorted!

## License Information

[`nxtlvlsoftware/php-static-constructors`](https://github.com/NxtLvlSoftware/php-static-constructors)
is open-sourced software, freely available to use under the terms of the [MIT License](https://www.techtarget.com/whatis/definition/MIT-License-X11-license-or-MIT-X-license).

__A full copy of the license is available [here](https://github.com/NxtLvLSoftware/php-static-constructors/blob/dev/LICENSE).__

> THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
> IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
> FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
> AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
> LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
> OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
> SOFTWARE.

<br>
<hr>
<br>

__A [NxtLvL Software Solutions](https://github.com/NxtLvLSoftware) product.__
