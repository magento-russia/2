<?php
class Df_Catalog_Helper_Product_Url extends Mage_Catalog_Helper_Product_Url {
	/**
	 * Цель перекрытия —
	 * улучшить транслитерацию букв русского алфавита по сравнению с Magento CE.
	 *
	 * Обратите внимание, что родительский класс Mage_Catalog_Helper_Product_Url
	 * не является потомком класса Varien_Object,
	 * поэтому у нашего класса нет метода _construct,
	 * и мы перекрываем именно конструктор
	 * @override
	 * @return Df_Catalog_Helper_Product_Url
	 */
	public function __construct() {
		if (df_cfg()->seo()->common()->getEnhancedRussianTransliteration()) {
			$this->_convertTable =
				array_merge($this->_convertTable, $this->getRussianUpdatesLc()
			);
		}
		parent::__construct();
	}


	/**
	 * @param string $string
	 * @return string
	 */
	public function extendedFormat($string) {
		return
			df_cfg()->seo()->urls()->getPreserveCyrillic()
			? df_output()->formatUrlKeyPreservingCyrillic($string)
			: df_output()->transliterate($string)
		;
	}

	/**
	 * @param string $string
	 * @return string
	 */
	public function formatPreservingCase($string) {
		return strtr($string, $this->getConversionTablePreservingCase());
	}

	/** @return string[] */
	private function getConversionTablePreservingCase() {
 		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array_merge($this->_convertTable, $this->getRussianUpdates());
		}
		return $this->{__METHOD__};
	}

	/** @return string[] */
	private function getRussianUpdates() {
 		if (!isset($this->{__METHOD__})) {
			/** @var string[] $values */
			$values = array_values(self::$_russianUpdatesRaw);
			/** @var string[] $keys */
			$keys = array_keys(self::$_russianUpdatesRaw);
			$this->{__METHOD__} = array_merge(
				array_combine($keys, $values)
				,array_combine(df_t()->strtolower($keys), df_t()->strtolower($values))
			);
		}
		return $this->{__METHOD__};
	}

	/** @return string[] */
	private function getRussianUpdatesLc() {
 		if (!isset($this->{__METHOD__})) {
			/** @var string[] $values */
			$values = df_t()->strtolower(array_values(self::$_russianUpdatesRaw));
			/** @var string[] $keys */
			$keys = array_keys(self::$_russianUpdatesRaw);
			$this->{__METHOD__} = array_merge(
				array_combine($keys, $values)
				,array_combine(df_t()->strtolower($keys), $values)
			);
		}
		return $this->{__METHOD__};
	}

	/** @var string[] */
	private static $_russianUpdatesRaw =
		array(
			  'А' => 'A'
			, 'Б' => 'B'
			, 'В' => 'V'
			, 'Г' => 'G'
			, 'Д' => 'D'
			, 'Е' => 'E'
			, 'Ё' => 'YO'
			, 'Ж' => 'ZH'
			, 'З' => 'Z'
			, 'И' => 'I'
			, 'Й' => 'J'
			, 'К' => 'K'
			, 'Л' => 'L'
			, 'М' => 'M'
			, 'Н' => 'N'
			, 'О' => 'O'
			, 'П' => 'P'
			, 'Р' => 'R'
			, 'С' => 'S'
			, 'Т' => 'T'
			, 'У' => 'U'
			, 'Ф' => 'F'
			, 'Х' => 'H'
			, 'Ц' => 'C'
			, 'Ч' => 'CH'
			, 'Ш' => 'SH'
			, 'Щ' => 'SCH'
			, 'Ъ' => ''
			, 'Ы' => 'Y'
			, 'Ь' => ''
			, 'Э' => 'E'
			, "Ю" => "JU"
			, 'Я' => 'YA'
			, "ЬЕ" => "JE"
			, "ЬЯ" => "YA"
			, "ЬЁ" => "JO"
			, "ЬЮ" => "JU"
			, "ЪЕ" => "JE"
			, "ЪЯ" => "JA"
			, "ЪЁ" => "JO"
			, "ЪЮ" => "JU"
		)
	;

	/** @return Df_Catalog_Helper_Product_Url */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}