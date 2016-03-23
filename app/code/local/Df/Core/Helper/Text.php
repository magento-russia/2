<?php
class Df_Core_Helper_Text extends Mage_Core_Helper_Abstract {
	/**
	 * @param string $json
	 * @return string
	 */
	public function adjustCyrillicInJson($json) {
		$trans = array(
			'\u0430'=>'а', '\u0431'=>'б', '\u0432'=>'в', '\u0433'=>'г','\u0434'=>'д'
			, '\u0435'=>'е', '\u0451'=>'ё', '\u0436'=>'ж','\u0437'=>'з', '\u0438'=>'и'
			, '\u0439'=>'й', '\u043a'=>'к','\u043b'=>'л', '\u043c'=>'м'
			, '\u043d'=>'н', '\u043e'=>'о','\u043f'=>'п', '\u0440'=>'р', '\u0441'=>'с'
			, '\u0442'=>'т','\u0443'=>'у', '\u0444'=>'ф', '\u0445'=>'х', '\u0446'=>'ц'
			,'\u0447'=>'ч', '\u0448'=>'ш', '\u0449'=>'щ', '\u044a'=>'ъ','\u044b'=>'ы'
			, '\u044c'=>'ь', '\u044d'=>'э', '\u044e'=>'ю','\u044f'=>'я','\u0410'=>'А'
			, '\u0411'=>'Б', '\u0412'=>'В', '\u0413'=>'Г','\u0414'=>'Д', '\u0415'=>'Е'
			, '\u0401'=>'Ё', '\u0416'=>'Ж','\u0417'=>'З', '\u0418'=>'И', '\u0419'=>'Й'
			, '\u041a'=>'К','\u041b'=>'Л', '\u041c'=>'М', '\u041d'=>'Н', '\u041e'=>'О'
			,'\u041f'=>'П', '\u0420'=>'Р', '\u0421'=>'С', '\u0422'=>'Т','\u0423'=>'У'
			, '\u0424'=>'Ф', '\u0425'=>'Х', '\u0426'=>'Ц','\u0427'=>'Ч', '\u0428'=>'Ш'
			, '\u0429'=>'Щ', '\u042a'=>'Ъ','\u042b'=>'Ы', '\u042c'=>'Ь', '\u042d'=>'Э'
			, '\u042e'=>'Ю','\u042f'=>'Я','\u0456'=>'і', '\u0406'=>'І', '\u0454'=>'є'
			, '\u0404'=>'Є','\u0457'=>'ї', '\u0407'=>'Ї', '\u0491'=>'ґ', '\u0490'=>'Ґ'
		);
		return strtr($json, $trans);
	}

	/**
	 * @param string $string1
	 * @param string $string2
	 * @return bool
	 */
	public function areEqualCI($string1, $string2) {
		return 0 === strcmp(mb_strtolower($string1), mb_strtolower($string2));
	}

	/** @return string */
	public function bom() {return pack('CCC',0xef,0xbb,0xbf);}

	/**
	 * @param string $text
	 * @return string
	 */
	public function bomAdd($text) {
		return (mb_substr($text, 0, 3) === $this->bom()) ? $text : $this->bom() . $text;
	}

	/**
	 * @param string $text
	 * @return string
	 */
	public function bomRemove($text) {
		df_param_string($text, 0);
		/** @var string $result */
		$result =
			(mb_substr($text, 0, 3) === $this->bom())
			? mb_substr($text, 3)
			: $text
		;
		if (false === $result) {
			$result = '';
		}
		df_result_string($result);
		return $result;
	}

	/**
	 * @param string $text
	 * @return string
	 */
	public function camelize($text) {
		df_param_string($text, 0);
		return
			implode(
				array_map(
					array($this, 'ucfirst')
					,explode(
						Df_Core_Const::T_CONFIG_WORD_SEPARATOR
						,df_trim($text)
					)
				)
			)
		;
	}

	/**
	 * @param string $text
	 * @param int $requiredLength
	 * @param bool $addDots[optional]
	 * @return string
	 */
	public function chop($text, $requiredLength, $addDots = true) {
		df_param_string($text, 0);
		df_param_integer($requiredLength, 1);
		df_param_between($requiredLength, 1, 0);
		df_param_boolean($addDots, 2);
		return
			(mb_strlen($text) <= $requiredLength)
			? $text
			: rm_concat_clean(''
				,$this->trim(mb_substr($text, 0, $requiredLength - ($addDots ? 3 : 0)))
				,$addDots ? '...' : null
			)
		;
	}

	/**
	 * @param string|array(int|string => string) $text
	 * @return string|string[]
	 */
	public function convertUtf8ToWindows1251($text) {
		/** @var string|array(int|string => string) $result */
		$result = null;
		/** @var bool $firstParamIsArray */
		$firstParamIsArray = is_array($text);
		/** @var mixed[] $arguments */
		$arguments = $firstParamIsArray ? $text : func_get_args();
		if ((1 < count($arguments)) || $firstParamIsArray) {
			/**
			 * If the array argument contains string keys
			 * then the returned array will contain string keys
			 * if and only if exactly one array is passed.
			 * @see array_map
			 * @link http://php.net/manual/en/function.array-map.php
			 */
			$result = array_map(array($this, 'convertUtf8ToWindows1251'), $arguments);
		}
		else {
			/**
			 * Насколько я понимаю, данному вызову равноценно:
			 * iconv('utf-8', 'windows-1251', $string)
			 */
			$result = mb_convert_encoding($text, 'Windows-1251', 'UTF-8');
		}
		return $result;
	}

	/**
	 * @param string|array(int|string => string) $text
	 * @return string|string[]
	 */
	public function convertWindows1251ToUtf8($text) {
		/** @var string|array(int|string => string) $result */
		$result = null;
		/** @var bool $firstParamIsArray */
		$firstParamIsArray = is_array($text);
		/** @var mixed[] $arguments */
		$arguments = $firstParamIsArray ? $text : func_get_args();
		if ((1 < count($arguments)) || $firstParamIsArray) {
			$result = array_map(array($this, 'convertWindows1251ToUtf8'), $arguments);
		}
		else {
			/**
			 * Насколько я понимаю, данному вызову равноценно:
			 * iconv('utf-8', 'windows-1251', $string)
			 */
			$result = mb_convert_encoding($text, 'UTF-8', 'Windows-1251');
		}
		return $result;
	}

	/**
	 * @param string|string[] $data
	 * @param string[]|null $allowedTags [optional]
	 * @return string|string[]
	 */
	public function escapeHtml($data, $allowedTags = null) {
		/** @var string|string[] $result */
		$result =
			/**
			 * К сожалению, нельзя здесь для проверки публичности метода
			 * использовать @see is_callable,
			 * потому что наличие @see Varien_Object::__call
			 * приводит к тому, что is_callable всегда возвращает true.
			 */
			method_exists(df_mage()->coreHelper(), 'escapeHtml')
			? call_user_func(array(df_mage()->coreHelper(), 'escapeHtml'), $data, $allowedTags)
			: df_mage()->coreHelper()->htmlEscape($data, $allowedTags)
		;
		return $result;
	}

	/**
	 * @param string $text
	 * @param string $format
	 * @return string
	 */
	public function formatCase($text, $format) {
		/** @var string $result */
		$result = $text;
		switch($format) {
			case Df_Admin_Model_Config_Source_Format_Text_LetterCase::LOWERCASE:
				$result = mb_strtolower($result);
				break;
			case Df_Admin_Model_Config_Source_Format_Text_LetterCase::UPPERCASE:
				$result = mb_strtoupper($result);
				break;
			case Df_Admin_Model_Config_Source_Format_Text_LetterCase::UCFIRST:
				/**
				 * 2016-03-23
				 * Раньше алгоритм был таким:
				 * $result = df_text()->ucfirst(mb_strtolower(df_trim($result)));
				 * Это приводило к тому, что настроечная опция
				 * «Использовать ли HTTPS для административной части?»
				 * отображались как «Использовать ли https для административной части?».
				 * Уже не помню, зачем я ранее здесь использовал @see mb_strtolower
				 */
				$result = df_text()->ucfirst(df_trim($result));
				break;
		}
		/**
		 * Убрал валидацию результата намеренно: сам метод безобиден,
		 * и даже если он как-то неправильно будет работать — ничего страшного.
		 * Пока метод дал сбой только один раз, в магазине laap.ru
		 * при форматировании заголовков административной таблицы товаров
		 * (видимо, сбой произошёл из-за влияния некоего стороннего модуля).
		 */
		return $result;
	}

	/**
	 * @param string|float $float
	 * @param int $decimals [optional]
	 * @return string
	 */
	public function formatFloat($float, $decimals = 2) {
		return number_format(rm_float($float), $decimals, '.', '');
	}

	/**
	 * Этот метод не предназначен для интеграции со сторонними системами.
	 * Для интеграции со сторонними системами следует создавать отдельный метод
	 * в соответствующем сторонней системе модуле.
	 * @see Df_1C_Helper_Data::formatMoney
	 * @see Df_YandexMarket_Helper_Data::formatMoney
	 *
	 * @param string|float $money
	 * @return string
	 */
	public function formatMoney($money) {return $this->formatMoney($money, 2);}

	/**
	 * @param int $amount
	 * @param array $forms
	 * @return string
	 */
	public function getNounForm($amount, array $forms) {
		df_param_integer($amount, 0);
		return $this->getNounFormatter()->getForm($amount, $forms);
	}

	/** @return Df_Core_Model_Format_NounForAmounts */
	private function getNounFormatter() {return Df_Core_Model_Format_NounForAmounts::s();}

	/**
	 * @param string $text
	 * @return string
	 */
	public function htmlspecialchars($text) {
		$filter = new Df_Zf_Filter_HtmlSpecialChars();
		return $filter->filter($text);
	}

	/**
	 * @param string $text
	 * @return bool
	 */
	public function isMultiline($text) {
		return rm_contains($text, "\n") || rm_contains($text, "\r");
	}

	/**
	 * Простой, неполный, но практически адекватный для моих ситуаций
	 * способ опредилелить, является ли строка регулярным выражением.
	 * @param string $text
	 * @return string
	 */
	public function isRegex($text) {return rm_starts_with($text, '#');}

	/**
	 * @param string $text
	 * @return bool
	 */
	public function isTranslated($text) {
		if (!isset($this->{__METHOD__}[$text])) {
			/** @link http://stackoverflow.com/a/16130169 */
			$this->{__METHOD__}[$text] = !is_null(rm_preg_match('#[\p{Cyrillic}]#mu', $text, false));
		}
		return $this->{__METHOD__}[$text];
	}

	/**
	 * @param string $string
	 * @return string
	 */
	public function lcfirst($string) {
		return mb_strtolower(mb_substr($string, 0, 1)) . mb_substr($string, 1);
	}

	/**
	 * @param string $text
	 * @return string
	 */
	public function nl2br($text){
		/** @var string $result */
		$result = $text;
		if (rm_contains($text, '<pre>')) {
			$text = rm_normalize($text);
			$text = str_replace("\n", '{rm-newline}', $text);
			$text =
				preg_replace_callback(
					'#\<pre\>([\s\S]*)\<\/pre\>#mui'
					, array('self', 'nl2brCallback')
					, $text
				)
			;
			$result = strtr($text, array(
				'{rm-newline}' => '<br/>'
				,'{rm-newline-preserve}' => "\n"
			));
		}
		return $result;
	}

	/**
	 * @param string $text
	 * @return string
	 */
	public function noEscape($text) {
		return
			rm_starts_with($text, Df_Core_Helper_DataM::TAG__NO_ESCAPE)
			? $text
			: Df_Core_Helper_DataM::TAG__NO_ESCAPE . $text
		;
	}

	/**
	 * @param string $name
	 * @return string
	 */
	public function normalizeName($name) {return mb_strtoupper(df_trim($name));}

	/**
	 * Аналог @see str_pad() для Unicode.
	 * @link http://stackoverflow.com/a/14773638
	 * @param string $input
	 * @param int $pad_length
	 * @param string $pad_string
	 * @param int $pad_type
	 * @param string $encoding
	 * @return string
	 */
	public function pad($input, $pad_length, $pad_string = ' ', $pad_type = STR_PAD_RIGHT, $encoding = 'UTF-8') {
		/** @var string $result */
		/** @var int $input_length */
		$input_length = mb_strlen($input, $encoding);
		/** @var int $pad_string_length */
		$pad_string_length = mb_strlen($pad_string, $encoding);
		if ($pad_length <= 0 || ($pad_length - $input_length) <= 0) {
			$result = $input;
		}
		else {
			/** @var int $num_pad_chars */
			$num_pad_chars = $pad_length - $input_length;
			/** @var int $left_pad */
			/** @var int $right_pad */
			switch ($pad_type) {
				case STR_PAD_RIGHT:
					$left_pad = 0;
					$right_pad = $num_pad_chars;
					break;
				case STR_PAD_LEFT:
					$left_pad = $num_pad_chars;
					$right_pad = 0;
					break;
				case STR_PAD_BOTH:
					$left_pad = floor($num_pad_chars / 2);
					$right_pad = $num_pad_chars - $left_pad;
					break;
				default:
					df_error_internal();
					break;
			}
			$result = '';
			for ($i = 0; $i < $left_pad; ++$i) {
				$result .= mb_substr($pad_string, $i % $pad_string_length, 1, $encoding);
			}
			$result .= $input;
			for ($i = 0; $i < $right_pad; ++$i) {
				$result .= mb_substr($pad_string, $i % $pad_string_length, 1, $encoding);
			}
		}
		return $result;
	}

	/**
	 * @param string $text
	 * @param bool $needThrow [optional]
	 * @return int|null
	 */
	public function parseFirstInteger($text, $needThrow = true) {
		/** @var int|null $result */
		if (!df_check_string_not_empty($text)) {
			if ($needThrow) {
				df_error_internal('Не могу вычленить целое число из пустой строки.');
			}
			else {
				$result = null;
			}
		}
		else {
			$result = rm_preg_match_int('#(\d+)#m', $text, false);
			if (is_null($result) && $needThrow) {
				df_error_internal('Не могу вычленить целое число из строки «%s».', $text);
			}
		}
		return $result;
	}

	/**
	 * @param string $text
	 * @return string[]
	 */
	public function parseTextarea($text) {
		return df_clean(array_map('df_trim', explode("\n", rm_normalize(df_trim($text)))));
	}

	/**
	 * @param string|string[] $text
	 * @param string $type [optional]
	 * @return string|string[]
	 */
	public function quote($text, $type = self::QUOTE__RUSSIAN) {
		if ('"' === $type) {
			$type = self::QUOTE__DOUBLE;
		}
		else if ("'" === $type) {
			$type = self::QUOTE__SINGLE;
		}
		/** @var array(string => string[]) $quotesMap */
		static $quotesMap =
			array(
				self::QUOTE__DOUBLE => array('"', '"')
				,self::QUOTE__RUSSIAN => array('«', '»')
				,self::QUOTE__SINGLE => array('\'', '\'')
			)
		;
		/** @var string[] $quotes */
		$quotes = df_a($quotesMap, $type);
		if (!is_array($quotes)) {
			df_error_internal('Неизвестный тип кавычки «%s».', $type);
		}
		df_assert_array($quotes);
		$result =
			is_array($text)
			? df_map(array($this, 'quote'), $text, array($type))
			:
				/**
				 * Обратите внимание на красоту решения:
				 * мы «склеиваем кавычки»,
				 * используя в качестве промежуточного звена исходную строку
				 */
				implode($text, $quotes)
		;
		return $result;
	}

	/**
	 * Удаляет с начала каждой строки текста заданное количество пробелов
	 * @param string $text
	 * @param int $numSpaces
	 * @return string
	 */
	public function removeLeadingSpacesMultiline($text, $numSpaces) {
		return implode(explode(str_repeat(' ', $numSpaces), $text));
	}

	/**
	 * 2015-03-03
	 * Алгоритм аналогичен @see singleLine()
	 *
	 * 2015-07-07
	 * Раньше алгоритм был таким:
	 	return strtr($text, "\r\n", '  ');
	 * Однако он не совсем правилен,
	 * потому что если перенос строки записан в формате Windows
	 * (то есть, в качестве переноса строки используется последовательность \r\n),
	 * то прошлый алгоритм заменит эту последовательность на 2 пробела, а надо — на один.
	 *
	 * «If given three arguments,
	 * this function returns a copy of str where all occurrences of each (single-byte) character in from
	 * have been translated to the corresponding character in to,
	 * i.e., every occurrence of $from[$n] has been replaced with $to[$n],
	 * where $n is a valid offset in both arguments.
	 * If from and to have different lengths,
	 * the extra characters in the longer of the two are ignored.
	 * The length of str will be the same as the return value's.»
	 * @link http://php.net/strtr
	 *
	 * Новый алгоритм взял отсюда:
	 * @link http://stackoverflow.com/a/20717751
	 *
	 * @param string $text
	 * @return string
	 */
	public function removeLineBreaks($text) {
		/** @var string[] $symbolsToRemove */
		static $symbolsToRemove = array("\r\n", "\r", "\n");
		return str_replace($symbolsToRemove, ' ', $text);
	}

	/**
	 * @link http://www.php.net/str_ireplace
	 *
	 * @param string $search
	 * @param string $replace
	 * @param string $subject
	 * @param int|null $count[optional]
	 * @return string
	 */
	public function replaceCI($search, $replace, $subject, $count = null) {
		if (!is_array($search)) {
			$slen = mb_strlen($search);
			if (0 === $slen) {
				return $subject;
			}

			$lendif = mb_strlen($replace) - mb_strlen($search);
			$search = mb_strtolower($search);
			$search = preg_quote($search);
			$lstr = mb_strtolower($subject);
			$i = 0;
			$matched = 0;
			/** @var string[] $matches */
			$matches = array();
			while (1 === preg_match('/(.*)'.$search.'/Us',$lstr, $matches)) {
				if ($i === $count ) {
					break;
				}
				$mlen = mb_strlen($matches[0]);
				$lstr = mb_substr($lstr, $mlen);
				$subject =
					substr_replace(
						$subject, $replace, $matched+strlen($matches[1]), $slen
					)
				;
				$matched += $mlen + $lendif;
				$i++;
			}
			return $subject;
		}
		else {
			foreach (array_keys($search) as $k ) {
				if (is_array($replace)) {
					if (array_key_exists($k,$replace)) {
						$subject =
							$this->replaceCI(
								$search[$k], $replace[$k], $subject, $count
							)
						;
					} else {
						$subject = $this->replaceCI($search[$k], '', $subject, $count);
					}
				} else {
					$subject = $this->replaceCI($search[$k], $replace, $subject, $count);
				}
			}
			return $subject;
		}
	}

	/**
	 * @param string $text
	 * @return string[]
	 */
	public function splitOnLines($text) {return explode("\n", rm_normalize($text));}

	/**
	 *
	 * @param string $text
	 * @param string $charlist[optional]
	 * @return string
	 */
	public function trim($text, $charlist = null) {
		if (!is_null($charlist)) {
			/** @var string[] $addionalSymbolsToTrim */
			$addionalSymbolsToTrim = array("\n", "\r", ' ');
			foreach ($addionalSymbolsToTrim as $addionalSymbolToTrim) {
				/** @var string $addionalSymbolToTrim */
				if (!rm_contains($charlist, $addionalSymbolToTrim)) {
					$charlist = df_concat($charlist, $addionalSymbolToTrim);
				}
			}
		}
		/**
		 * Обратите внимание, что класс Zend_Filter_StringTrim может работать некорректно
		 * для строк, заканчивающихся заглавной кириллической буквой «Р».
		 * @link http://framework.zend.com/issues/browse/ZF-11223
		 * Однако решение, которое предложено по ссылке выше
		 * (@link http://framework.zend.com/issues/browse/ZF-11223)
		 * может приводить к падению интерпретатора PHP
		 * для строк, начинающихся с заглавной кириллической буквы «Р».
		 * Такое у меня происходило в методе @see Df_Autotrading_Model_Request_Locations::parseLocation()
		 * Кто виноват: решение или исходный класс Zend_Filter_StringTrim — не знаю
		 * (скорее, решение).
		 * Поэтому мой класс Df_Zf_Filter_StringTrim дополняет решение по ссылке выше
		 * программным кодом из Zend Framework 2.0.
		 */
		$filter = new Df_Zf_Filter_StringTrim($charlist);
		/** @var Df_Zf_Filter_StringTrim $filter */
		/** @var string $result */
		$result = $filter->filter($text);
		/**
		 * Zend_Filter_StringTrim::filter теоретически может вернуть null,
		 * потому что этот метод зачастую перепоручает вычисление результата функции preg_replace
		 * @url http://php.net/manual/en/function.preg-replace.php
		 */
		$result = df_nts($result);
		// Как ни странно, Zend_Filter_StringTrim иногда выдаёт результат « ».
		if (' ' === $result) {
			$result = '';
		}
		return $result;
	}

	/**
	 * @param string $string
	 * @return string
	 */
	public function ucfirst($string) {
		return mb_strtoupper(mb_substr($string, 0, 1)) . mb_substr($string, 1);
	}

	/**
	 * Источник алгоритма:
	 * @link http://stackoverflow.com/a/14338869
	 * @param string $string1
	 * @param string $string2
	 * @return string
	 */
	public function xor_($string1, $string2) {
		return bin2hex(pack('H*', $string1) ^ pack('H*', $string2));
	}

	const _CLASS = __CLASS__;
	const QUOTE__DOUBLE = 'double';
	const QUOTE__RUSSIAN = 'russian';
	const QUOTE__SINGLE = 'single';

	/** @return Df_Core_Helper_Text */
	public static function s() {static $r; return $r ? $r : $r = new self;}

	/**
	 * @param string[] $matches
	 * @return string
	 */
	private static function nl2brCallback(array $matches) {
		return str_replace('{rm-newline}', '{rm-newline-preserve}', df_a($matches, 0, ''));
	}
}