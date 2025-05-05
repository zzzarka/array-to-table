<?php

declare(strict_types=1);

namespace ArrayToTable;

readonly class AsciiTableColorize
{
    public const int BLACK = 0;
    public const int WHITE = 255;
    public const int DARK_GRAY = 236;
    public const int GRAY = 238;
    public const int LIGHT_GRAY = 236;

    public function __construct(
        public array $dataLinesBackground = [self::DARK_GRAY, self::GRAY],
        public int $headerBackground = self::BLACK,
        public int $separationLinesBackground = self::BLACK,
        public int $textColor = self::WHITE,
        public int $resetTextColor = self::WHITE,
        public int $resetColorBackground = self::DARK_GRAY,
    ) {
    }

    public function echoTextColor()
    {
        echo "\e[38;5;" . $this->textColor . "m";
    }

    public function echoSeparationColor()
    {
        echo "\e[48;5;" . $this->separationLinesBackground . "m";
    }

    public function echoHeaderColor()
    {
        echo "\e[48;5;" . $this->headerBackground . "m";
    }

    public function echoDataLineColor(int $dataLineCounter)
    {
        echo "\e[48;5;" . $this->dataLinesBackground[$dataLineCounter % count($this->dataLinesBackground)] . "m";
    }

    public function echoResetColorBackground()
    {
        echo "\e[0m";
        $this->echoTextColor();
        //echo "\e[48;5;" . $this->resetColorBackground . "m";
    }

    public function echoResetColorText()
    {
        echo "\e[38;5;" . $this->resetTextColor . "m";
    }

    public function resetAll()
    {
        echo "\e[0m";
    }
}
