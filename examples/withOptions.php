<?php

declare(strict_types=1);

require_once __DIR__ . "/../vendor/autoload.php";

use ArrayToTable\TableOptions;
use ArrayToTable\CliTableRender;

$table = new CliTableRender();

$tableData = [
    ["Test", "Test 2", "test 3"],
    ["1", "foo", "bar\nbar2"],
    ["2", "second col"],
    [],
    [0 => "2", 2 => "third col that is too long"],
];
$table->printResultTable($tableData, new TableOptions(renderNewlines: true, cutLongTextAfter: 20, showRowCount: false));

$str = $table->printResultTableToString(
    $tableData,
    new TableOptions(renderNewlines: true, wrapTextAfter: 20, emptyRowIsSeparator: false, showRowCount: false)
);

echo "from string: " . PHP_EOL . $str;