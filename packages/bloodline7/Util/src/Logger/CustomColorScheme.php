<?php

	namespace Ausumsports\Admin\Logger;

	use Bramus\Monolog\Formatter\ColorSchemes\DefaultScheme;
	use Monolog\Logger;
	use Bramus\Ansi\Ansi;
	use Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;

	class CustomColorScheme extends DefaultScheme
	{
		public function __construct() {

			parent::__construct();

			$this->setColorizeArray([
				Logger::DEBUG => $this->ansi->color(SGR::COLOR_FG_WHITE)->get(),
				Logger::INFO => $this->ansi->color(SGR::COLOR_FG_GREEN)->get(),
				Logger::NOTICE => $this->ansi->color(SGR::COLOR_FG_CYAN)->get(),
				Logger::WARNING => $this->ansi->color(SGR::COLOR_FG_YELLOW)->get(),
				Logger::ERROR => $this->ansi->color(SGR::COLOR_FG_RED)->get(),
				Logger::CRITICAL => $this->ansi->color(SGR::COLOR_FG_RED)->underline()->get(),
				Logger::ALERT => $this->ansi->color([SGR::COLOR_FG_YELLOW, SGR::COLOR_BG_BLUE])->get(),
				Logger::EMERGENCY => $this->ansi->color(SGR::COLOR_BG_RED)->blink()->color(SGR::COLOR_FG_BLACK)->get(),
			]);
		}
	}
