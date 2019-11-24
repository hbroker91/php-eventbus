PHP EventBus
=======================

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/hbroker91/php-eventbus/badges/quality-score.png?b=refactor)](https://scrutinizer-ci.com/g/hbroker91/php-eventbus/?branch=refactor)
[![Build Status](https://scrutinizer-ci.com/g/hbroker91/php-eventbus/badges/build.png?b=refactor)](https://scrutinizer-ci.com/g/hbroker91/php-eventbus/build-status/refactor)
![License](https://img.shields.io/github/license/hbroker91/php-eventbus?style=flat-square)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/hbroker91/php-eventbus/badges/code-intelligence.svg?b=refactor)](https://scrutinizer-ci.com/code-intelligence)

Simple package to add lifecycle hook(s) to any PHP class.

The package consists of two files, an interface and a trait (mixin). In the case of the former, the class must implement the
methods provided with the interface, in the latter case it just mixin in the codebase of the class.

The main purpose of this tiny package is, to add one or two interaction point(s) to a class, before its' instantiation or after its' destruction or both. 
It is useful to check, if various preconditions has been met before allow the execution to run further in the application, _e.g. request data checking_ before calling the class' constructor to instantiate it. 

Requirements
============

* PHP >= 7.0
* OOP style programmig
* Willingness to spare couple of CPU cycles :)

Installation
============
    composer require hbroker91/php-hookifier
Usage
=====
Implement the ```Hookable``` interface in the class where the hook(s) needed, or use the ```Hookify``` trait with ```use``` keyword.

**Examples:**
```php
namespace Some\Namespace;

use Hbroker91\PHPHookifier\Hookable;

class UserModel extends Model implements Hookable {

    /** @var string */
    private $userName;
    
    /** @var string */
    private $userId;
    
    /** @var string */
    private $emailAddress;
    ...
    
    // if the func. evaluates to false, throw an Exception somewhere at application's boot
    // otherwise allow this class to instantiate and potentially fill up with data 
    // (in this case this class represents a model).
        
    public static function beforeConstruct(... $options): bool 
    {
        [$payload] = $options;
        return isset($payload['userData']));
    }
    
    public static function afterDestroy(... $options): bool
    {
        // do some very important thing
        // if it went ok, return true otherwise false
        return true;
    }
}
```
Using the ```trait``` is similar, for example:
```php

namespace Some\Namespace;

use Hbroker91\PHPHookifier\Hookify;

class UserModel extends Model {

    use Hookify;

    /** @var string */
    private $userName;
    
    /** @var string */
    private $userId;
    
    /** @var string */
    private $emailAddress;
    ...
    
    // if the func. evaluates to false, throw an Exception somewhere at application's boot
    // otherwise allow this class to instantiate and potentially fill up with data 
    // (in this case this class represents a model).
        
    public static function beforeConstruct(... $options): bool 
    {
        [$payload] = $options;
        return isset($payload['userData']));
    }
    
    public static function afterDestroy(... $options): bool {}
}
```
In this case the ```afterDestroy``` body is empty, it isn't used.

Pretty straightforward.

Footnote
=======

If you found it useful, plase give a :star:, thank you.

Got questions, ideas, improvement tips? 

Feel free to contact me.


