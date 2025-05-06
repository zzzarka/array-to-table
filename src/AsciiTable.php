<?php

declare(strict_types=1);

namespace ArrayToTable;

class AsciiTable
{
    public function __construct(private ?AsciiTableStyle $style = null, private ?TableOptions $options = null)
    {
        $this->style = $this->style ?? new AsciiTableStyle();
        $this->options = $this->options ?? new TableOptions();
    }

    public function printResultTableToString(array $rows): string
    {
        ob_start();
        $this->printResultTable($rows);
        return ob_get_clean();
    }

    public function printResultTable(array $rows): void
    {
        $header = $this->options->getHeaderRow($rows);
        $this->style->colors?->echoTextColor();

        [$rows, $extraRows] = $this->preprocessNewLinesToRows($rows);

        $colWidths = $this->getMaxColCharacters(
            array_merge([$header], $rows), //TODO: expand and merge $extraRows
            $this->style->vertical,
            $this->options->maxColumnChars()
        );

        if ($this->options->showRowCount) {
            echo "Row count: " . count($rows) . PHP_EOL;
        }
        if ($this->options->outsideLines) {
            $this->style->colors?->echoSeparationColor();
            $this->drawLine($colWidths, true, 0);
        }

        if ($header) {
            $this->style->colors?->echoHeaderColor();
            echo $this->style->vertical;
            foreach ($header as $k => $col) {
                $str = mb_str_pad($col, $colWidths[$k], " ");
                echo mb_str_pad($str, $colWidths[$k] + ($this->options->paddingH * 2), " ", STR_PAD_BOTH);
                echo $this->style->vertical;
            }
            $this->style->colors?->echoResetColorBackground();
            echo PHP_EOL;
            $this->style->colors?->echoSeparationColor();
            $this->drawLine($colWidths, true);
        }

        end($rows);
        $lastRowKey = key($rows);
        reset($rows);
        $dataLineCounter = 0;
        foreach ($rows as $rowKey => $row) {
            if (empty($row)) {
                if ($this->options->emptyRowIsSeparator) {
                    $this->style->colors?->echoSeparationColor();
                    $this->drawLine($colWidths, true);
                }
                continue;
            }
            $dataLineCounter++;

            $this->style->colors?->echoDataLineColor($dataLineCounter);
            echo $this->style->vertical;

            foreach ($colWidths as $k => $width) {
                $value = $row[$k] ?? '';
                if (is_numeric($value)) {
                    $colStr = mb_str_pad((string) $value, $colWidths[$k], " ", STR_PAD_LEFT);
                    echo mb_str_pad($colStr, $width + ($this->options->paddingH * 2), " ", STR_PAD_BOTH);
                    echo $this->style->vertical;
                } else {
                    $colStr = mb_str_pad($value, $width, " ");
                    echo mb_str_pad($colStr, $width + ($this->options->paddingH * 2), " ", STR_PAD_BOTH);
                    echo $this->style->vertical;
                }
            }

            $this->style->colors?->echoResetColorBackground();
            echo PHP_EOL;

            foreach ($extraRows[$rowKey] ?? [] as $extraRow) {
                $this->style->colors?->echoDataLineColor($dataLineCounter);
                echo $this->style->vertical;
                foreach ($colWidths as $k => $width) {
                    $colStr = mb_str_pad($extraRow[$k] ?? " ", $width, " ");
                    echo mb_str_pad($colStr, $width + ($this->options->paddingH * 2), " ", STR_PAD_BOTH);
                    echo $this->style->vertical;
                }
                $this->style->colors?->echoResetColorBackground();
                echo PHP_EOL;
            }

            if ($rowKey === $lastRowKey && $this->options->outsideLines) {
                $this->style->colors?->echoSeparationColor();
                $this->drawLine($colWidths, true, 2);
            } elseif ($this->options->lineBetweenRows) {
                $this->style->colors?->echoSeparationColor();
                $this->drawLine($colWidths);
            }
        }

        $this->style->colors?->resetAll();
    }

    private function drawLine(array $colWidths, bool $wide = false, int $position = 1): void
    {
        $str = '';
        $first = true;
        foreach ($colWidths as $k => $w) {
            if ($position === 0) {
                $str .= ($first ? $this->style->cornerTL : $this->style->topIntersectW);
            } elseif ($position === 1) {
                $str .= ($first ?
                    ($wide ? $this->style->midCornerWL : $this->style->midCornerSL) :
                    ($wide ? $this->style->midIntersectW : $this->style->midIntersectS));
            } elseif ($position === 2) {
                $str .= ($first ? $this->style->cornerBL : $this->style->midIntersectW);
            }

            $printChars = $w + (mb_strlen($this->style->vertical) - 1);

            for ($i = 0; $i < $printChars + ($this->options->paddingH * 2); $i++) {
                $str .= $wide ? $this->style->horisontalHeader : $this->style->horisontal;
            }
        }

        if ($position === 0) {
            $str .= $this->style->cornerTR;
        } elseif ($position === 1) {
            $str .= $wide ? $this->style->midCornerWR : $this->style->midCornerSR;
        } elseif ($position === 2) {
            $str .= $this->style->cornerBR;
        }

        echo $str;
        $this->style->colors?->echoResetColorBackground();
        echo PHP_EOL;
    }

    private function getMaxColCharacters(array $rows, string $separator, int $maxLen = 0): array
    {
        if ($maxLen > 0) {
            $maxLen += mb_strlen($separator);
        }
        $colLengths = [];
        foreach ($rows as $row) {
            foreach ($row as $k => $value) {
                $len = mb_strlen((string) $value);
                if (!isset($colLengths[$k])) {
                    $colLengths[$k] = $len;
                } elseif ($len > $colLengths[$k]) {
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

    private function escapeNewline(string $str): string
    {
        return str_replace(["\n", "\r"], [$this->options->replaceNewlineWith, ''], $str);
    }

    private function preprocessNewLinesToRows(array $rows): array
    {
        $extraRows = [];
        foreach ($rows as $outerRowKey => $row) {
            if (empty($row)) {
                continue;
            }
            foreach ($row as $k => $col) {
                if ($this->options->cutLongTextAfter && mb_strlen($col) > $this->options->cutLongTextAfter) {
                    $col = mb_substr($col, 0, $this->options->cutLongTextAfter) . '...';
                }

                if ($this->options->replaceVerticalWith !== null) {
                    $col = str_replace('|', $this->options->replaceVerticalWith, $col);
                }

                if ($this->options->newlinesIsNewRows) {
                    //TODO: leaks \r linebreaks in case of \r\n
                    $lines = explode("\n", (string) $col);
                } else {
                    $lines = [$col];
                }

                $linesAgain = [];
                foreach ($lines as $l) {
                    $escapedLine = $this->escapeNewline($l);
                    if ($this->options->wrapTextAfter && mb_strlen($escapedLine) > $this->options->wrapTextAfter) {
                        //TODO: use word wrap instead of char wrap .. but need to find multibyte capable version
                        $subDivided = mb_str_split($escapedLine, $this->options->wrapTextAfter);
                        foreach ($subDivided as $division) {
                            $linesAgain[] = $division;
                        }
                    } else {
                        $linesAgain[] = $escapedLine;
                    }
                }
                $rows[$outerRowKey][$k] = array_shift($linesAgain);
                if ($linesAgain) {
                    foreach ($linesAgain as $rowKey => $line) {
                        $extraRows[$outerRowKey][$rowKey][$k] = $line;
                    }
                }
            }
        }

        return [$rows, $extraRows];
    }
}
