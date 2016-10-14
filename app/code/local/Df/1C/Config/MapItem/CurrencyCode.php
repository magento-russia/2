<?php
class Df_1C_Config_MapItem_CurrencyCode extends Df_Admin_Config_MapItem {
	/** @return string */
	public function getNonStandard() {return df_nts($this->cfg(self::P__NON_STANDARD));}

	/** @return string */
	public function getNonStandardNormalized() {
		return rm_1c_currency_code_normalize($this->getNonStandard());
	}

	/**
	 * Обратите внимание, что ключ @see Df_1C_Config_MapItem_CurrencyCode::P__STANDARD
	 * может отсутствовать в массиве:
	 * http://magento-forum.ru/topic/4893/
	 * Такое возможно даже в двух ситуациях
	 * при неряшливом заполнении таблицы
	 * «Нестандартные символьные коды валют»
	 * администратором магазина:
	 * 1) когда администратор добавил в эту таблицу пустую строку в конец.
	 * 2) когда администратор указал для строки таблицы
	 * лишь нестандартный сивольный код (первая колонка таблицы),
	 * не указав стандартный символьный код (вторая колонка таблицы).
	 * @see Df_Admin_Config_MapItem::isValid()
	 * @override
	 * @return string
	 */
	public function getStandard() {return df_nts($this->cfg(self::P__STANDARD));}

	/** @return bool */
	public function isValid() {return $this->getStandard() && $this->getNonStandard();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__NON_STANDARD, DF_V_STRING, false)
			->_prop(self::P__STANDARD, DF_V_STRING, false)
		;
	}
	/** @used-by Df_1C_Config_Block_NonStandardCurrencyCodes::_construct() */
	const P__NON_STANDARD = 'non_standard_code';
	/** @used-by Df_1C_Config_Block_NonStandardCurrencyCodes::_construct() */
	const P__STANDARD = 'standard_code';

	/**
	 * 2015-04-18
	 * Описывает поля структуры данных.
	 * Используется для распаковки значений по умолчанию.
	 * @used-by Df_Admin_Config_Backend_Table::unserialize()
	 * @return string[]
	 */
	public static function fields() {return array(self::P__NON_STANDARD, self::P__STANDARD);}
}