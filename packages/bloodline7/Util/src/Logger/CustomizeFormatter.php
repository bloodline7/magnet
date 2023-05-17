<?php

	namespace Ausumsports\Admin\Logger;

	use Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;
	use Monolog\Logger;

	use Monolog\Formatter\LineFormatter;
	use Ausumsports\Admin\Logger\CustomColorScheme;
	use Bramus\Ansi\Ansi;
	use Bramus\Ansi\Writers\BufferWriter;
	use Monolog\Utils;
	use Ausumsports\Admin\Events\Logger as EventLogger;


	class CustomizeFormatter extends LineFormatter
	{
		//public const SIMPLE_FORMAT = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";
		/**
		 * ANSI Wrapper which provides colors
		 * @var \Bramus\Ansi\Ansi
		 */
		protected $ansi = null;
		public const SIMPLE_FORMAT = "[%datetime%] |%level_name%| %message% %context% %extra%\n";

		//private $jsonEncodeOptions = JSON_UNESCAPED_SLASHES| JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR;
		private $jsonEncodeOptions = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR;
		/**
		 * Return the JSON representation of a value
		 *
		 * @param mixed $data
		 * @return string            if encoding fails and ignoreErrors is true 'null' is returned
		 * @throws \RuntimeException if encoding fails and errors are not ignored
		 */
		protected function toJson($data, bool $ignoreErrors = false): string {
			return Utils::jsonEncode($data, $this->jsonEncodeOptions, $ignoreErrors);
		}

		public function __construct($format = null, $dateFormat = "Y.m.d H:i:s", $allowInlineLineBreaks = false, $ignoreEmptyContextAndExtra = true) {
			// Call Parent Constructor
			$this->colorScheme = new CustomColorScheme();
			$this->ansi = new Ansi(new BufferWriter());
			parent::__construct($format, $dateFormat, $allowInlineLineBreaks, $ignoreEmptyContextAndExtra);
		}

		/**
		 * {@inheritdoc}
		 */
		public function format(array $record): string {

			//$record['extra']['url'] =  request()->getUri();
			return $this->recodeReplace($record);
		}

		public function strPad($strVal, $len=10, $space=' ')
		{
			$strVal = trim($this->stringify($strVal));
			$valLen = mb_strLen($strVal);
			$startBlank = str_pad( '', ceil( ($len - $valLen) / 2 ) , $space);
			$endBlank = str_pad( '', $len - (mb_strLen($startBlank) + $valLen) , $space);

			return $startBlank . $strVal . $endBlank;

		}


		/**
		 * {@inheritdoc}
		 */
		public function recodeReplace(array $record): string {
			$vars = $this->normalize($record);

			$output = $this->format;

			foreach ($vars['extra'] as $var => $val) {
				if (false !== strpos($output, '%extra.' . $var . '%')) {

					$extra = $this->ansi->color(SGR::COLOR_FG_BLUE)->text($this->stringify($val))->nostyle()->get();
					$output = str_replace('%extra.' . $var . '%', $extra, $output);
					unset($vars['extra'][$var]);
				}
			}

			foreach ($vars['context'] as $var => $val) {
				if (false !== strpos($output, '%context.' . $var . '%')) {
					$output = str_replace('%context.' . $var . '%', $this->stringify($val), $output);
					unset($vars['context'][$var]);
				}
			}

			if ($this->ignoreEmptyContextAndExtra) {
				if (empty($vars['context'])) {
					unset($vars['context']);
					$output = str_replace('%context%', '', $output);
				}

				if (empty($vars['extra'])) {
					unset($vars['extra']);
					$output = str_replace('%extra%', '', $output);
				}
			}

			foreach ($vars as $var => $val) {
				if (false !== strpos($output, '%' . $var . '%')) {
					switch ($var){
						case 'datetime' :
							if (false !== strpos($output, '[%datetime%]')) {
								$dateTime = $this->ansi->color(SGR::COLOR_FG_CYAN)->text('[' . $val . ']')->nostyle()->get();
								$output = str_replace('[%datetime%]', $dateTime, $output);
							}
							break;


						case 'extra' :

							$extra = $this->ansi->color(SGR::COLOR_FG_GREEN_BRIGHT)->text($this->stringify($val))->nostyle()->get();
							$output = str_replace('%' . $var . '%', $extra, $output);


							break;

						case 'channel' :
							switch ($val){
								case 'local' :
								case 'test' :
									$channel = $this->ansi->color(SGR::COLOR_FG_WHITE_BRIGHT)->text('[' . $val . ']')->nostyle()->get();
									break;
								default :
									$channel = $this->ansi->color(SGR::COLOR_FG_RED)->text('[' . $val . ']')->nostyle()->get();
									break;
							}

							$output = str_replace('%' . $var . '%', $this->strPad($channel, 7), $output);
							break;

						case 'level_name' :
							$val = $this->colorScheme->getColorizeString($record['level']) . $this->strPad($val, 11 ) .  $this->colorScheme->getResetString();
							$output = str_replace('%' . $var . '%', $val, $output);
							break;

						default :
							$val = $this->colorScheme->getColorizeString($record['level']) .  ' ' . $this->stringify($val) . ' ' . $this->colorScheme->getResetString();
							$output = str_replace('%' . $var . '%', $val, $output);
							break;
					}

				}
			}

			// remove leftover %extra.xxx% and %context.xxx% if any
			if (false !== strpos($output, '%')) {
				$output = preg_replace('/%(?:extra|context)\..+?%/', '', $output);
			}

			broadcast(new EventLogger($output));
			return $output;
		}

	}
