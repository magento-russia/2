<?php
class Df_Core_Helper_Output extends Mage_Core_Helper_Abstract {
	/**
	 * @param string $text
	 * @return string
	 */
	public function _($text) {return rm_e($text);}

	/**
	 * @param string $string
	 * @return string
	 */
	public function formatUrlKeyPreservingCyrillic($string) {
		return trim (preg_replace('/[^\pL\pN]+/u','-', mb_strtolower($string)),'-');
	}

	/**
	 * @param string $xml
	 * @return string
	 */
	public function formatXml($xml) {
		df_param_string($xml, 0);
		/** @var DOMDocument $domDocument */
		$domDocument = new DOMDocument();
		/** @var bool $r */
		$r = $domDocument->loadXML($xml);
		df_assert(TRUE === $r);
		$domDocument->formatOutput = true;
		/** @var string $result */
		$result = $domDocument->saveXML();
		df_result_string($result);
		return $result;
	}

	/**
	 * @param string[] $cssClasses
	 * @return string
	 */
	public function getCssClassesAsString(array $cssClasses) {return implode(' ', $cssClasses);}

	/** @return string */
	public function getXmlHeader() {return '<'.'?xml version="1.0" encoding="UTF-8"?'.'>'."\n";}

	/**
	 * @param mixed[] $data
	 * @return string
	 */
	public function json(array $data) {
		return Zend_Json::encode($data, $this->getJsonEncoderOptions());
	}

	/**
	 * @param string|null $string
	 * @param string $delimiter [optional]
	 * @return string[]
	 */
	public function parseCsv($string, $delimiter = ',') {
		return !$string ? array() : df_trim(explode($delimiter, $string));
	}

	/**
	 * Пребразует строку вида «превед [[медвед]]» в «превед <a href="http://yandex.ru">медвед</a>».
	 * @used-by Df_Admin_Model_Notifier::getMessage()
	 * @used-by Df_Admin_Model_Notifier_Settings::getMessage()
	 * @param string $text
	 * @param string $url
	 * @param string $quote [optional]
	 * @return string
	 */
	public function processLink($text, $url, $quote = Df_Core_Helper_Text::QUOTE__DOUBLE) {
		return
			!rm_contains($text, '[[')
			? $text
			: preg_replace("#\[\[([^\]]+)\]\]#u", rm_tag('a', array('href' => $url), '$1'), $text)
		;
	}

	/**
	 * @param string $string
	 * @return string
	 */
	public function transliterate($string) {
		return
			trim(
				strtolower(
					preg_replace(
						'#[^0-9a-z]+#i'
						,'-'
						,df_mage()->catalog()->product()->urlHelper()->format($string)
					)
				)
				,'-'
			)
		;
	}

	/** @return int */
	private function getJsonEncoderOptions() {
		if (!isset($this->{__METHOD__})) {
			/** @var int $result */
			$result = 0;
			/**
			 * Использование кавычек обязательно!
			 * http://php.net/manual/function.defined.php (пример 1)
			 * http://magento-forum.ru/topic/4190/
			 */
			if (defined('JSON_FORCE_OBJECT')) {
				$result |= JSON_FORCE_OBJECT;
			}
			if (defined('JSON_UNESCAPED_UNICODE')) {
				$result |= JSON_UNESCAPED_UNICODE;
			}
			if (defined('JSON_NUMERIC_CHECK')) {
				$result |= JSON_NUMERIC_CHECK;
			}
			if (defined('JSON_PRETTY_PRINT')) {
				$result |= JSON_PRETTY_PRINT;
			}
			if (defined('JSON_PRETTY_PRINT')) {
				$result |= JSON_PRETTY_PRINT;
			}
			if (defined('JSON_FORCE_OBJECT')) {
				$result |= JSON_FORCE_OBJECT;
			}
			if (defined('JSON_UNESCAPED_SLASHES')) {
				$result |= JSON_UNESCAPED_SLASHES;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Core_Helper_Output */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}