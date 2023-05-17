<?php

namespace Bloodline7\Logger;

use Bramus\Monolog\Formatter\ColorSchemes\DefaultScheme;
use Monolog\Level;
use Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;

class CustomColorScheme extends DefaultScheme
{

    private array $bgColorScheme;

    public function __construct()
    {
        // Call Trait Constructor, so that we have $this->ansi available
        parent::__construct();

        // Our Color Scheme
        $this->setColorizeArray(array(
            Level::Debug->value => $this->ansi->color([SGR::COLOR_FG_WHITE])->get(),
            Level::Info->value => $this->ansi->color([SGR::COLOR_FG_GREEN])->get(),
            Level::Notice->value => $this->ansi->color([SGR::COLOR_FG_CYAN])->get(),
            Level::Warning->value => $this->ansi->color([SGR::COLOR_FG_YELLOW])->get(),
            Level::Error->value => $this->ansi->color([SGR::COLOR_FG_RED])->get(),
            Level::Critical->value => $this->ansi->color([SGR::COLOR_FG_PURPLE])->get(),
            Level::Alert->value => $this->ansi->color([SGR::COLOR_FG_BLUE_BRIGHT])->get(),
            Level::Emergency->value => $this->ansi->color([SGR::COLOR_BG_RED_BRIGHT, SGR::COLOR_FG_WHITE_BRIGHT])->blink()->get(),
        ));


        $this->setBgColorizeArray(array(
            Level::Debug->value => $this->ansi->color([SGR::COLOR_BG_WHITE_BRIGHT, SGR::COLOR_FG_BLACK])->get(),
            Level::Info->value => $this->ansi->color([SGR::COLOR_BG_GREEN_BRIGHT, SGR::COLOR_FG_WHITE_BRIGHT])->get(),
            Level::Notice->value => $this->ansi->color([SGR::COLOR_BG_CYAN, SGR::COLOR_FG_WHITE_BRIGHT])->get(),
            Level::Warning->value => $this->ansi->color([SGR::COLOR_BG_YELLOW, SGR::COLOR_FG_WHITE_BRIGHT])->get(),
            Level::Error->value => $this->ansi->color([SGR::COLOR_BG_RED_BRIGHT, SGR::COLOR_FG_WHITE_BRIGHT])->get(),
            Level::Critical->value => $this->ansi->color([SGR::COLOR_BG_PURPLE, SGR::COLOR_FG_WHITE_BRIGHT])->get(),
            Level::Alert->value => $this->ansi->color([SGR::COLOR_BG_BLUE, SGR::COLOR_FG_WHITE_BRIGHT])->get(),
            Level::Emergency->value => $this->ansi->color([SGR::COLOR_BG_RED, SGR::COLOR_FG_YELLOW_BRIGHT])->blink()->get(),
        ));


    }

    public function setBgColorizeArray(array $colorScheme): void
    {
        // Only store entries that exist as Monolog\Logger levels
        $colorScheme = array_intersect_key($colorScheme, array_combine(Level::VALUES, Level::NAMES));

        // Store the filtered colorScheme
        $this->bgColorScheme = $colorScheme;
    }

    /**
     * Get the Color Scheme String for the given Level
     * @param int $level The Logger Level
     * @return string The Color Scheme String
     */
    public function getBgColorizeString($level): string
    {
        return $this->bgColorScheme[$level] ?? '';
    }
}
