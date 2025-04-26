<?php

declare(strict_types=1);

namespace ArrayToTable;

class TableOptions
{
    public function __construct(
        public readonly int $cutLongTextAfter = 48,
        public readonly int $wrapTextAfter = 0,
        public readonly bool $emptyRowIsSeparator = true,
        public readonly bool $showRowCount = true,
        public readonly bool $firstRowIsHeader = true,
        public readonly bool $renderNewlines = false,
    ) {
        if ($this->cutLongTextAfter < 0) {
            throw new \InvalidArgumentException('cutLongTextAfter must be greater than or equal to 0');
        }
        if ($this->wrapTextAfter < 0) {
            throw new \InvalidArgumentException('wrapTextAfter must be greater than or equal to 0');
        }
    }

    public function maxColumnChars(): int
    {
        return ($this->wrapTextAfter <= 0 ? $this->cutLongTextAfter : $this->wrapTextAfter);
    }
}
