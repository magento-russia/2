<?php
class Df_YandexMarket_Model_Config_Backend_Currency extends Df_Admin_Config_Backend_Currency {
	/**
	 * @override
	 * @return string
	 */
	public function getFailureMessageTemplate() {
		return
		 	'Модулю «{module}» нужно работать с {currencyInstrumental},'
			.'<br/>потому что именно эта валюта сейчас указана в графе'
			.' «В какой валюте передавать Яндекс.Маркету цены на товары?»'
			.'<br/>раздела «Общие настройки» модуля «{module}».'
			.'<br/>1) Убедитесь, что валюта «{currency}» включена для {stores}.'
			/**
			 * Раньше с формой «куда» была проблема:
			 * для казахского тенге Морфер возвращал «на казахского тенге».
			 * Однако это вроде исправлено:
			 * http://morpher.ru/WebService.aspx#msg2515
			 */
			.'<br/>2) Укажите курс обмена {baseCurrenciesOrigin} на {currencyInFormDestionation}.'
			.'<br/>Вместо этого Вы также можете указать другую валюту в графе'
			.' «В какой валюте передавать Яндекс.Маркету цены на товары?».'
		;
	}

	/**
	 * @override
	 * @return string
	 */
	public function getModuleName() {return 'Яндекс.Маркет';}

	/**
	 * @see Df_Admin_Config_Backend_Currency::_beforeSave()
	 * @overide
	 * @return Df_YandexMarket_Model_Config_Backend_Currency
	 */
	protected function _beforeSave() {
		// Выполняем проверки только при включенности модуля.
		if ($this->getValue()) {
			Df_YandexMarket_Model_Config_Source_Currency::check($this->getValue());
		}
		parent::_beforeSave();
		return $this;
	}
}