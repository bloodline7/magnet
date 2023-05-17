<?php

	namespace Bloodline7\Logger;

	use Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;
	use Monolog\Logger;

	use Monolog\Formatter\LineFormatter;
	use Bloodline7\Logger\CustomColorScheme;
	use Bramus\Ansi\Ansi;
	use Bramus\Ansi\Writers\BufferWriter;
    use Monolog\LogRecord;
    use Monolog\Utils;
    use Bloodline7\Logger\Events\Logger as EventLogger;
    use Predis\Client;
    use SebastianBergmann\Diff\Exception;
    use Bramus\Ansi\ControlSequences\EscapeSequences\Enums\EL;


    class CustomizeFormatter extends LineFormatter
	{
		//public const SIMPLE_FORMAT = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";
		/**
		 * ANSI Wrapper which provides colors
		 * @var \Bramus\Ansi\Ansi
		 */
		protected $ansi = null;
		public const SIMPLE_FORMAT = "[%datetime%] %level_name% %message% %context% %extra%\n";

	//	private $jsonEncodeOptions = JSON_UNESCAPED_SLASHES| JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR;
		private string|int $jsonEncodeOptions = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR;
        private \Bloodline7\Logger\CustomColorScheme $colorScheme;
        private bool $broadcast;
        private Client $redis;

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

		public function __construct(?string $format = null, ?string $dateFormat = "Y-m-d H:i:s", bool $allowInlineLineBreaks = false, bool $ignoreEmptyContextAndExtra = true, bool $includeStacktraces = false)
        {
            parent::__construct($format, $dateFormat, $allowInlineLineBreaks, $ignoreEmptyContextAndExtra, $includeStacktraces);

			$this->colorScheme = new CustomColorScheme();
			$this->ansi = new Ansi(new BufferWriter());

            $this->redis = new Client([
                'scheme' => 'tcp',
                'host'   => env("REDIS_HOST", '127.0.0.1'),
                'port'   => env("REDIS_PORT", 6379),
            ]);


            $this->broadcast = true;

		}

		public function strPad($strVal, $len=10, $space=' '): string
        {
			$strVal = trim($this->stringify($strVal));
			$valLen = mb_strLen($strVal);
			$startBlank = str_pad( '', ceil( ($len - $valLen) / 2 ) , $space);
			$endBlank = str_pad( '', $len - (mb_strLen($startBlank) + $valLen) , $space);

			return $startBlank . $strVal . $endBlank;

		}


        function format(LogRecord $record): string
        {
            $output = $this->recodeReplace($record);
            $replaced = $this->getEraseCode().$output;

            if(app()->runningInConsole()) echo $replaced;
            else if($this->broadcast)
                broadcast(new EventLogger($replaced));

            return $output;

        }


        function getEraseCode(): string
        {

            $return = "";

            try {
                if($line = $this->redis->get('printPercentLine')) {

                    for($i=0; $i<$line; $i++) {
                        $return .= $this->ansi->cuu()->el(EL::ALL)->get();
                    }


                    $this->redis->set('printPercentLine', 0);

                }
            }
            catch (\Exception $exception)
            {

                return '';
            }


            return $return;
        }


        public function recodeReplace(LogRecord $record): string
        {

            $vars = $this->normalizeRecord($record);

            $output = $this->format;


            foreach ($vars['extra'] as $var => $val) {
				if (str_contains($output, '%extra.' . $var . '%')) {

					$extra = $this->ansi->color(SGR::COLOR_FG_BLUE)->text($this->stringify($val))->nostyle()->get();
					$output = str_replace('%extra.' . $var . '%', $extra, $output);
					unset($vars['extra'][$var]);
				}
			}

			foreach ($vars['context'] as $var => $val) {
				if (str_contains($output, '%context.' . $var . '%')) {
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
				if (str_contains($output, '%' . $var . '%')) {
					switch ($var){
						case 'datetime' :
							if (str_contains($output, '[%datetime%]')) {
								$dateTime = $this->ansi->color(SGR::COLOR_FG_CYAN)->text('[' . $val . ']')->nostyle()->get();
								$output = str_replace('[%datetime%]', $dateTime, $output);
							}
							break;

                        case 'extra' :

						case 'context' :
                            $context = "\n".$this->ansi->color(SGR::COLOR_FG_BLACK_BRIGHT)->text('---------------------------------------------')->nostyle()->get()
                                ."\n" .$this->ansi->color(SGR::COLOR_FG_GREEN_BRIGHT)->text($this->stringify($val))->nostyle()->get()
                                ."\n".$this->ansi->color(SGR::COLOR_FG_BLACK_BRIGHT)->text('---------------------------------------------')->nostyle()->get();

							$output = str_replace('%' . $var . '%', $context , $output);
							break;

						case 'channel' :

							$channel = match ($val) {
                                'local', 'test' => $this->ansi->color(SGR::COLOR_FG_WHITE_BRIGHT)->text('[' . $val . ']')->nostyle()->get(),
                                default => $this->ansi->color(SGR::COLOR_FG_RED)->text('[' . $val . ']')->nostyle()->get(),
                            };

							$output = str_replace('%' . $var . '%', $this->strPad($channel, 7), $output);
							break;


						case 'level_name' :
							$val = $this->colorScheme->getBgColorizeString($record['level']) . $this->strPad($val, 11 ) .  $this->colorScheme->getResetString();
							$output = str_replace('%' . $var . '%', $val, $output);
							break;


						default :
							$val = $this->colorScheme->getColorizeString($record['level']) .  '' . $this->stringify($val) . '' . $this->colorScheme->getResetString();
							$output = str_replace('%' . $var . '%', $val, $output);
							break;
					}

				}
			}

			// remove leftover %extra.xxx% and %context.xxx% if any
			if (str_contains($output, '%')) {
				$output = preg_replace('/%(?:extra|context)\..+?%/', '', $output);
			}

			return $output;
		}

	}
