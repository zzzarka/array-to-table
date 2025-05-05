<?php

declare(strict_types=1);

namespace ArrayToTable;

readonly class TableOptions
{
    public static function markdown($firstRowIsHeader = true, $wrapTextAfter = 0): self
    {
        return new self(
            cutLongTextAfter: 0,
            wrapTextAfter: $wrapTextAfter,
            emptyRowIsSeparator: false,
            showRowCount: false,
            firstRowIsHeader: $firstRowIsHeader,
            newlinesIsNewRows: false,
            replaceNewlineWith: '<br>',
            replaceVerticalWith: '\|', // '&#124;'
            headerColumns: [],
            lineBetweenRows: false,
            outsideLines: false,
            paddingH: 0,
        );
    }

    public function __construct(
        public int $cutLongTextAfter = 48,
        public int $wrapTextAfter = 0,
        public bool $emptyRowIsSeparator = true,
        public bool $showRowCount = true,
        public bool $firstRowIsHeader = true,
        public bool $newlinesIsNewRows = false,
        public ?string $replaceNewlineWith = '\\n',
        public ?string $replaceVerticalWith = null,
        public array $headerColumns = [],
        public bool $lineBetweenRows = false,
        public bool $outsideLines = true,
        public int $paddingH = 1,
    ) {
        if ($this->cutLongTextAfter < 0) {
            throw new \InvalidArgumentException('cutLongTextAfter must be greater than or equal to 0');
        }
        if ($this->wrapTextAfter < 0) {
            throw new \InvalidArgumentException('wrapTextAfter must be greater than or equal to 0');
        }
        if ($this->paddingH < 0) {
            throw new \InvalidArgumentException('paddingH must be greater than or equal to 0');
        }
    }

    public function maxColumnChars(): int
    {
        return ($this->wrapTextAfter <= 0 ? $this->cutLongTextAfter : $this->wrapTextAfter);
    }

    public function getHeaderRow(array &$rows)
    {
        if ($this->headerColumns) {
            return $this->headerColumns;
        } elseif ($this->firstRowIsHeader) {
            return array_shift($rows);
        }

        return [];
    }
}
