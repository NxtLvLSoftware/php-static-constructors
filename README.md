Static Constructors for PHP
===============
_Brings static class constructors to PHP!_

[![Build Status](https://travis-ci.org/NxtLvLSoftware/php-static-constructors.svg?branch=master)](https://travis-ci.org/NxtLvLSoftware/php-static-constructors)

### About

This package brings a feature present in most other common programming languages to PHP, static constructors. This is
implemented by searching a class for a static method with the same name as the class (case sensitive) and making sure the
method accepts no arguments (helps to keep compatibility with existing code and you shouldn't be passing in arguments anyway).

Here's a quick example:
```php
class Example {
    private static function Example()
    {
        echo "Hello World!" . PHP_EOL;
    }
}

new Example;
```

You can probably already guess the output of this code, the console will output `Hello World!`. The first time the class
or any child classes are referenced in your code the static constructor will be called. Due to the way PHP loads classes
parent constructors will be called first:

```php
abstract class Parent {
    private static function Parent() { echo "Called first" . PHP_EOL; }
}

class Child extends Parent {
    private static function Child() { echo "Called second" . PHP_EOL; }
}

new Child;
```

We use private static methods in the examples as it avoids other classes calling the constructor methods, currently you
can use all three visibility modifiers (public, protected and public) but this may change in a future version.

### Installation

All you have to do to install with composer is the following:

```bash
$ composer require nxtlvlsoftware/static-constructors
```

Or add it directly to your composer.json manifest:

```json
{
    "require": {
        "nxtlvlsoftware/static-constructors": "*"
    }
}
```

Composer will automatically handle 'hooking' the library for you when you require your `vendor/autoload.php` file. If you
use another autoloader in your project you can disable the automatic hook by adding `define('DISABLE_STATIC_HOOK', true)`
anywhere before you `include` the composer autoload file. You must then hook the library yourself with `\nxtlvlsoftware\statics\StaticConstructors::init()`.
Be careful delaying the library initialisation though as any class loaded before the library is started will not have it's
static constructor called so it's best to let compose handle it for you.

### Under the hood

This is all implemented by registering our own class loader and unregistering any previously registered loaders. When our
loader is called we do the job PHP normally does, loop over all the actual class loaders to try and load the class but
this is where we perform the magic that allows us to use static constructors. If a class is loaded by one of the real
loaders, we look for a suitable static constructor method on the loaded class using reflection. If we find a constructor
then we simply call it! PHP does most of the dirty work for us, if a class extends another then the parent class is automatically
loaded by PHP before the child so we don't even have to walk up the inheritance chain ourselves and since PHP only uses
autoloader's to load a class into the runtime once the constructors are only ever called the first time the class is referenced.

### Issues

Found a problem with static constructors? Make sure to open an issue on the [issue tracker](https://github.com/NxtLvLSoftware/php-static-constructors/issues) and we'll get it sorted!


__The content of this repo is licensed under the Unlicense. A full copy of the license is available [here](LICENSE).__