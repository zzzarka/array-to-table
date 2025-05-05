<?php

declare(strict_types=1);

require_once __DIR__ . "/../vendor/autoload.php";

use ArrayToTable\AsciiTableStyle;
use ArrayToTable\TableOptions;
use ArrayToTable\AsciiTable;

$table = new AsciiTable(
    AsciiTableStyle::simpleAscii(),
    new TableOptions(newlinesIsNewRows: true, cutLongTextAfter: 20, showRowCount: false)
);

$tableData = [
    ["Test", "Test 2", "test 3"],
    ["1", "foo", "bar\nbar2"],
    ["2", "second col"],
    [],
    [0 => "2", 2 => "third col that is too long"],
];
$table->printResultTable($tableData);

//*

echo PHP_EOL . "Same but as string: " . PHP_EOL;
$str = $table->printResultTableToString(
    $tableData,
);

echo $str . PHP_EOL;

echo PHP_EOL;

echo "Line between rows, fancy" . PHP_EOL;
$table = new AsciiTable(
    null,
    new TableOptions(lineBetweenRows: true)
);
$table->printResultTable([
    ["Test", "Test 2", "test 3"],
    ["1", "foo", "bar"],
    ["2", "second col"],
    [0 => "2", 2 => "third col"],
]);
//*/
