# Flag :golf:

Binarized flags are not intuitive to understand, using concepts like
[bitwise operators](http://php.net/manual/en/language.operators.bitwise.php),
[bitmask](https://en.wikipedia.org/wiki/Mask_(computing)) or [Bit field](https://en.wikipedia.org/wiki/Bit_field).
Moreover, theses flags are not easy to debug; find flags that hide behind integer bitfield is very annoying.
This lib propose a fluent API to handle bitfield and improve developer experience with tools for debugging them.

[![Build Status](https://travis-ci.org/maidmaid/flag.svg?branch=master)](https://travis-ci.org/maidmaid/flag) 
[![Latest Stable Version](https://poser.pugx.org/maidmaid/flag/v/stable)](https://packagist.org/packages/maidmaid/flag)
[![License](https://poser.pugx.org/maidmaid/flag/license)](https://packagist.org/packages/maidmaid/flag)

## Installation

Use [Composer](http://getcomposer.org/) to install Flag in your project:

```shell
composer require "maidmaid/flag"
```

## Overview

For example, here is how to play with [``Yaml`` flags](https://github.com/symfony/symfony/blob/8872833c5d6a46ea27a4483e650617361660d946/src/Symfony/Component/Yaml/Yaml.php#L23-L34):

```php
use Maidmaid\Flag\Flag;
use Symfony\Component\Yaml\Yaml;

$flag = Flag::create(Yaml::class)
    ->add(Yaml::DUMP_OBJECT)      // logs '[debug] bitfield changed Yaml [bin: 1] [dec: 1] [flags: DUMP_OBJECT]'
    ->add(Yaml::PARSE_DATETIME)   // logs '[debug] bitfield changed Yaml [bin: 100001] [dec: 33] [flags: DUMP_OBJECT | PARSE_DATETIME]'
    ->remove(Yaml::DUMP_OBJECT)   // logs '[debug] bitfield changed Yaml [bin: 100000] [dec: 32] [flags: PARSE_DATETIME]'
;

$flag->has(Yaml::DUMP_OBJECT);    // returns false
$flag->has(Yaml::PARSE_DATETIME); // returns true
$flag->get();                     // returns 288
$flag->set(100);                  // logs '[debug] bitfield changed Yaml [bin: 1100100] [dec: 100] [flags: PARSE_OBJECT | PARSE_DATETIME | DUMP_OBJECT_AS_MAP]'

foreach ($flag as $k => $v) {
    echo "$k => $v ";            // writes '4 => PARSE_OBJECT 32 => PARSE_DATETIME 64 => DUMP_OBJECT_AS_MAP '
}
```

## Prefixed Flags

As in [``Caster::EXCLUDE_*`` case](https://github.com/symfony/symfony/blob/8872833c5d6a46ea27a4483e650617361660d946/src/Symfony/Component/VarDumper/Caster/Caster.php#L25-L34), it's possible to handle prefixed flags.

```php
use Maidmaid\Flag\Flag;
use Symfony\Component\VarDumper\Caster\Caster;

$flag = Flag::create(Caster::class, 'EXCLUDE_')
    ->add(Caster::EXCLUDE_EMPTY)         // logs '[debug] bitfield changed Caster::EXCLUDE_* [bin: 10000000] [dec: 128] [EXCLUDE_*: EMPTY]'
    ->add(Caster::EXCLUDE_PRIVATE)       // logs '[debug] bitfield changed Caster::EXCLUDE_* [bin: 10100000] [dec: 160] [EXCLUDE_*: PRIVATE | EMPTY]'
    ->add(Caster::EXCLUDE_NOT_IMPORTANT) // logs '[debug] bitfield changed Caster::EXCLUDE_* [bin: 110100000] [dec: 416] [EXCLUDE_*: PRIVATE | EMPTY | NOT_IMPORTANT]'
;
```
## Hierarchical Flags

As in [``Output::VERBOSITY_*`` case](https://github.com/symfony/symfony/blob/8872833c5d6a46ea27a4483e650617361660d946/src/Symfony/Component/Console/Output/OutputInterface.php#L23-L27), flags are hierachical, like this:

```
VERBOSITY_VERY_VERBOSE
└── VERBOSITY_VERBOSE
    └── VERBOSITY_NORMAL
```

This means that if ``VERBOSITY_VERY_VERBOSE`` is flagged, ``VERBOSITY_VERBOSE`` and ``VERBOSITY_NORMAL`` will be also implicitly flagged.

```php
use Symfony\Component\Console\Output\Output;
use Maidmaid\Flag\Flag;

$flag = Flag::create(Output::class, 'VERBOSITY_', $hierachical = true)
    ->add(Output::VERBOSITY_VERBOSE) // logs '[debug] bitfield changed Output::VERBOSITY_* [bin: 1000000] [dec: 64] [VERBOSITY_*: QUIET | NORMAL | VERBOSE]'
    ->add(Output::VERBOSITY_DEBUG)   // logs '[debug] bitfield changed Output::VERBOSITY_* [bin: 101000000] [dec: 320] [VERBOSITY_*: QUIET | NORMAL | VERBOSE | VERY_VERBOSE | DEBUG]'
;
```

## Global Flags

It's possible to handle flags in global space, like with [``E_*`` errors flags](http://php.net/manual/en/errorfunc.constants.php).

```php
use Maidmaid\Flag\Flag;

$flag = Flag::create(null, 'E_')
    ->add(E_ALL)             // logs '[debug] bitfield changed E_* [bin: 111111111111111] [dec: 32767] [E_*: ERROR | RECOVERABLE_ERROR | WARNING | PARSE | NOTICE | STRICT | DEPRECATED | CORE_ERROR | CORE_WARNING | COMPILE_ERROR | COMPILE_WARNING | USER_ERROR | USER_WARNING | USER_NOTICE | USER_DEPRECATED | ALL]'
    ->set(0)                 // logs '[debug] bitfield changed E_* [bin: 0] [dec: 0] [E_*: ]'
    ->add(E_USER_ERROR)      // logs '[debug] bitfield changed E_* [bin: 100000000] [dec: 256] [E_*: USER_ERROR]'
    ->add(E_USER_DEPRECATED) // logs '[debug] bitfield changed E_* [bin: 100000100000000] [dec: 16640] [E_*: USER_ERROR | USER_DEPRECATED]'
;
```

## No-int Flags

As in [``Request::METHOD_*`` case](https://github.com/symfony/symfony/blob/8872833c5d6a46ea27a4483e650617361660d946/src/Symfony/Component/HttpFoundation/Request.php#L49-L58), values flags are not integer but string. For example, ``METHOD_GET`` has ``GET`` string as value. This string values are internally binarized.

```php
use Maidmaid\Flag\Flag;
use Symfony\Component\HttpFoundation\Request;

$flag = Flag::create(Request::class, 'METHOD_')
    ->add(Request::METHOD_GET)  // logs '[debug] bitfield changed Request::METHOD_* [bin: 10] [dec: 2] [METHOD_*: GET]'
    ->add(Request::METHOD_POST) // logs '[debug] bitfield changed Request::METHOD_* [bin: 110] [dec: 6] [METHOD_*: GET | POST]'
    ->add(Request::METHOD_PUT)  // logs '[debug] bitfield changed Request::METHOD_* [bin: 1110] [dec: 14] [METHOD_*: GET | POST | PUT]'
;
```

## Standalone Flags

It's also possible to handle no-constants flags.

```php
use Maidmaid\Flag\Flag;

$flag = Flag::create()
    ->add('a') // logs '[debug] bitfield changed [bin: 1] [dec: 1] [flags: a]'
    ->add('b') // logs '[debug] bitfield changed [bin: 11] [dec: 3] [flags: a | b]'
;

$flag = (new Flag())
    ->add(8)  // logs '[debug] bitfield changed [bin: 1000] [dec: 8] [flags: 8]'
    ->add(32) // logs '[debug] bitfield changed [bin: 101000] [dec: 40] [flags: 8 | 32]'
;
```
