<?php

declare(strict_types=1);

namespace ArrayToTable;

readonly class AsciiTableStyle
{
    public static function prettyAscii()
    {
        return new self(colors: new AsciiTableColorize());
    }

    public static function simpleAscii(?AsciiTableColorize $colors = null): self
    {
        return new self(
            '-',
            '-',
            '|',
            '+',
            '+',
            '+',
            '+',
            '+',
            '+',
            '+',
            '+',
            '+',
            '+',
            '+',
            '+',
            $colors
        );
    }

    public static function markdown(): self
    {
        return new self(
            '-',
            '-',
            '|',
            '|',
            '|',
            '|',
            '|',
            '|',
            '|',
            '|',
            '|',
            '|',
            '|',
            '|',
            '|',
        );
    }

    public function __construct(
        public string $horisontalHeader = '═',
        public string $horisontal = '─',
        public string $vertical = '│',
        public string $cornerTL = '╒',
        public string $cornerTR = '╕',
        public string $cornerBL = '╘',
        public string $cornerBR = '╛',
        public string $topIntersectW = '╤',
        public string $bottomIntersectW = '╧',
        public string $midCornerWL = '╞',
        public string $midIntersectW = '╪',
        public string $midCornerWR = '╡',
        public string $midCornerSL = '├',
        public string $midIntersectS = '┼',
        public string $midCornerSR = '┤',
        public ?AsciiTableColorize $colors = null,
    ) {
    }
}
