<?php

declare(strict_types=1);

namespace ArrayToTable;

class CliTableRender
{
    private const TABLE_SEP = ' | ';
    private const EXTEND_TABLE = ' > ';

    public function printResultTableToString(array $rows, ?TableOptions $options = null): string
    {
        ob_start();
        $this->printResultTable($rows, $options);
        return ob_get_clean();
    }

    public function printResultTable(array $rows, ?TableOptions $options = null): void
    {
        $options = $options ?? new TableOptions();

        if ($options->renderNewlines) {
            $newRows = [];
            foreach ($rows as $row) {
                if (empty($row)) {
                    if ($options->emptyRowIsSeparator) {
                        $newRows[] = [];
                    }
                    continue;
                }
                $extraRows = [];
                $newCols = [];
                foreach ($row as $k => $col) {
                    $lines = explode("\n", (string) $col);
                    $newCols[$k] = array_shift($lines);
                    if ($lines) {
                        foreach ($lines as $rowKey => $line) {
                            $extraRows[$rowKey][$k] = $line;
                        }
                    }
                }
                $newRows[] = $newCols;
                foreach ($extraRows as $extraCols) {
                    $cols = [];
                    foreach ($extraCols as $k => $col) {
                        $cols[$k] = $col;
                    }
                    $newRows[] = $cols;
                }
            }
            $rows = $newRows;
        }

        $colWidths = $this->getMaxColWidths($rows, $options->maxColumnChars());
        if ($options->showRowCount) {
            echo " | Row count: " . count($rows) . PHP_EOL;
        }
        $this->drawLine($colWidths);
        if ($options->firstRowIsHeader) {
            echo self::TABLE_SEP;
            $header = array_shift($rows);
            foreach ($header as $k => $col) {
                echo mb_str_pad($col, $colWidths[$k], " ") . self::TABLE_SEP;
            }
            echo PHP_EOL;
            $this->drawLine($colWidths);
        }

        foreach ($rows as $row) {
            if (empty($row)) {
                if ($options->emptyRowIsSeparator) {
                    $this->drawLine($colWidths);
                }
                continue;
            }
            echo self::TABLE_SEP;
            $drawExtraRows = [];
            foreach ($colWidths as $k => $width) {
                $value = $row[$k] ?? '';
                if (is_numeric($value)) {
                    echo mb_str_pad((string) $value, $colWidths[$k], " ", STR_PAD_LEFT) . self::TABLE_SEP;
                } else {
                    $value = $this->escapeNewline((string) $value);

                    if ($options->cutLongTextAfter && mb_strlen($value) > $options->cutLongTextAfter) {
                        $value = substr($value, 0, $options->cutLongTextAfter) . '...';
                    }

                    $length = mb_strlen($value);
                    if ($options->wrapTextAfter <= 0 || $length < $options->wrapTextAfter) {
                        echo mb_str_pad($value, $colWidths[$k], " ") . self::TABLE_SEP;
                    } else {
                        $lines = mb_str_split($value, $colWidths[$k]);
                        $printNow = array_shift($lines);
                        echo mb_str_pad($printNow, $colWidths[$k], " ") . self::TABLE_SEP;

                        foreach ($lines as $rowKey => $l) {
                            $drawExtraRows[$rowKey][$k] = $l;
                        }
                    }
                }
            }
            echo PHP_EOL;

            foreach ($drawExtraRows as $extraRow) {
                echo self::EXTEND_TABLE;
                foreach ($colWidths as $k => $width) {
                    echo mb_str_pad($extraRow[$k] ?? " ", $colWidths[$k], " ") . self::EXTEND_TABLE;
                }
                echo PHP_EOL;
            }
        }
        $this->drawLine($colWidths);
    }

    private function getMaxColWidths(array $rows, int $maxLen = 0): array
    {
        if ($maxLen > 0) {
            $maxLen += mb_strlen(self::TABLE_SEP);
        }
        $colLengths = [];
        $header = array_shift($rows);
        foreach ($header as $k => $col) {
            $colLengths[$k] = mb_strlen($col);
        }
        foreach ($rows as $row) {
            foreach ($row as $k => $value) {
                $len = mb_strlen((string) $value);
                if ($len > $colLengths[$k]) {
                    if ($maxLen > 0 && $len > $maxLen) {
                        $colLengths[$k] = max($colLengths[$k], $maxLen);
                    } else {
                        $colLengths[$k] = $len;
                    }
                }
            }
        }

        return $colLengths;
    }

    private function drawLine(array $colWidths, string $char = '-'): void
    {
        $totalColumnWidth = array_sum($colWidths);
        $colCount = count($colWidths);
        $str = str_repeat($char, $totalColumnWidth + ($colCount * mb_strlen(self::TABLE_SEP)) + 1);
        if ($char === '-') {
            $next = 0;
            foreach ($colWidths as $w) {
                $str[$next] = '+';
                $next += $w + mb_strlen(self::TABLE_SEP);
            }
            $str[$next] = '+';
        }
        echo " " . $str . PHP_EOL;
    }

    private function escapeNewline(string $str): string
    {
        return str_replace(["\n", "\r"], ["\\n", "\\r"], $str);
    }
}
