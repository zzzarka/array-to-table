<?php

declare(strict_types=1);

require_once __DIR__ . "/../vendor/autoload.php";

use ArrayToTable\AsciiTableStyle;
use ArrayToTable\TableOptions;
use ArrayToTable\AsciiTable;

$tableData = [
    ["Test", "Test 2", "test 3"],
    ["1", "foo", "bar\nbar2"],
    ["2", "second col"],
    ["3", "third col"],
    ["4", "fourth col", "o yeah"],
    ["5", "fifth col", " this is a drag .. "],
    ["6", "sixth col", " .. "],
    [],
    [0 => "2", 2 => "third col that is too long"],
];
//*
$table = new AsciiTable(
    style: AsciiTableStyle::prettyAscii(),
    options: new TableOptions(newlinesIsNewRows: true, cutLongTextAfter: 20, showRowCount: false),
);
$table->printResultTable($tableData);

echo PHP_EOL;
//*/

echo "Lets try with wrapped text: " . PHP_EOL;

$table = new AsciiTable(
    style: AsciiTableStyle::prettyAscii(),
    options: new TableOptions(newlinesIsNewRows: false, wrapTextAfter: 10, cutLongTextAfter: 0, showRowCount: false),
);
$table->printResultTable($tableData);

echo PHP_EOL;

//*
echo "Line between rows, fancy" . PHP_EOL;
$table2 = new AsciiTable(
    style: AsciiTableStyle::prettyAscii(),
    options: new TableOptions(lineBetweenRows: true)
);
$table2->printResultTable([
    ["Test", "Test 2", "test 3"],
    ["1", "foo", "bar"],
    ["2", "second col"],
    [0 => "2", 2 => "third col"],
]);
//*/
