<?php

declare(strict_types=1);

require_once __DIR__ . "/../vendor/autoload.php";

use ArrayToTable\AsciiTable;
use ArrayToTable\AsciiTableStyle;
use ArrayToTable\TableOptions;

echo "Markdown format: " . PHP_EOL;
$mdTable = new AsciiTable(AsciiTableStyle::markdown(), TableOptions::markdown());
$tableData[2][1] = "second | col";
$mdTable->printResultTable([
    ["Test", "Test 2", "test 3"],
    ["1", "foo", "bar\nbar2"],
    ["2", "second | col"],
    [],
    [0 => "2", 2 => "third col that is too long"],
]);
