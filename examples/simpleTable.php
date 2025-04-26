<?php

declare(strict_types=1);

require_once __DIR__ . "/../vendor/autoload.php";

use ArrayToTable\CliTableRender;

$table = new CliTableRender();

$table->printResultTable([
    ["Test"],
    ["1"],
    ["2"],
]);

echo PHP_EOL;

$table->printResultTable([
    ["Test", "Test 2", "test 3"],
    ["1", "foo", "bar"],
    ["2", "second col"],
    [0 => "2", 2 => "third col"],
]);