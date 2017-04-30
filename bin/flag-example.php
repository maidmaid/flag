<?php

namespace Maidmaid\Flag\Demo;

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;
use Maidmaid\Flag\Flag;

$logger = new ConsoleLogger(new ConsoleOutput(ConsoleOutput::VERBOSITY_DEBUG));

echo "\nOverview\n";
class Yaml
{
    const DUMP_OBJECT = 1;
    const PARSE_EXCEPTION_ON_INVALID_TYPE = 2;
    const PARSE_OBJECT = 4;
    const PARSE_OBJECT_FOR_MAP = 8;
    const DUMP_EXCEPTION_ON_INVALID_TYPE = 16;
    const PARSE_DATETIME = 32;
    const DUMP_OBJECT_AS_MAP = 64;
    const DUMP_MULTI_LINE_LITERAL_BLOCK = 128;
    const PARSE_CONSTANT = 256;
}
$flag = Flag::create(Yaml::class);
$flag->setLogger($logger);
$flag
    ->add(Yaml::DUMP_OBJECT)
    ->add(Yaml::PARSE_DATETIME)
    ->remove(Yaml::DUMP_OBJECT)
;
var_dump(
    $flag->has(Yaml::DUMP_OBJECT),
    $flag->has(Yaml::PARSE_DATETIME),
    $flag->get()
);
$flag->set(100);
foreach ($flag as $k => $v) {
    echo "$k => $v ";
}

echo "\n\nExample\n";
class Color
{
    const RED = 1;
    const GREEN = 2;
    const YELLOW = 3;
    public $flag;

    public function __construct($logger)
    {
        $this->flag = Flag::create(self::class);
        $this->flag->setLogger($logger);
    }
}
(new Color($logger))->flag
    ->add(Color::RED)
    ->add(Color::GREEN)
    ->remove(Color::GREEN)
;

echo "\nPrefix\n";
class Caster
{
    const EXCLUDE_VERBOSE = 1;
    const EXCLUDE_VIRTUAL = 2;
    const EXCLUDE_DYNAMIC = 4;
    const EXCLUDE_PUBLIC = 8;
    const EXCLUDE_PROTECTED = 16;
    const EXCLUDE_PRIVATE = 32;
    const EXCLUDE_NULL = 64;
    const EXCLUDE_EMPTY = 128;
    const EXCLUDE_NOT_IMPORTANT = 256;
    const EXCLUDE_STRICT = 512;
}
$flag = Flag::create(Caster::class, 'EXCLUDE_');
$flag->setLogger($logger);
$flag
    ->add(Caster::EXCLUDE_EMPTY)
    ->add(Caster::EXCLUDE_PRIVATE)
    ->add(Caster::EXCLUDE_NOT_IMPORTANT)
    ->remove(Caster::EXCLUDE_NOT_IMPORTANT)
;

echo "\nHierarchical\n";
class Output
{
    const VERBOSITY_QUIET = 16;
    const VERBOSITY_NORMAL = 32;
    const VERBOSITY_VERBOSE = 64;
    const VERBOSITY_VERY_VERBOSE = 128;
    const VERBOSITY_DEBUG = 256;
}
$flag = Flag::create(Output::class, 'VERBOSITY_', true);
$flag->setLogger($logger);
$flag
    ->add(Output::VERBOSITY_VERBOSE)
    ->add(Output::VERBOSITY_DEBUG)
    ->remove(Output::VERBOSITY_DEBUG)
;

echo "\nGlobal space\n";
$flag = Flag::create(null, 'E_');
$flag->setLogger($logger);
$flag
    ->add(E_ALL)
    ->set(0)
    ->add(E_USER_ERROR)
    ->add(E_USER_DEPRECATED)
    ->remove(E_USER_DEPRECATED)
;

echo "\nBinarizedFlag\n";
class Request
{
    const METHOD_HEAD = 'HEAD';
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_PATCH = 'PATCH';
    const METHOD_DELETE = 'DELETE';
    const METHOD_PURGE = 'PURGE';
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_TRACE = 'TRACE';
    const METHOD_CONNECT = 'CONNECT';
}
$flag = Flag::create(Request::class, 'METHOD_');
$flag->setLogger($logger);
$flag
    ->add(Request::METHOD_GET)
    ->add(Request::METHOD_POST)
    ->add(Request::METHOD_PUT)
    ->remove(Request::METHOD_PUT)
;

echo "\nStandalone\n";
$flag = new Flag();
$flag->setLogger($logger);
$flag
    ->add(8)
    ->add(32)
    ->remove(32)
;
echo "\n";
$flag = Flag::create();
$flag->setLogger($logger);
$flag
    ->add('a')
    ->add('b')
    ->remove('b')
;
