<?php
class Df_YandexMarket_Model_System_Config_Backend_Currency
	/**
	 * Наследуемся именно от класса Df_Admin_Model_Config_Backend,
	 * чтобы в методе _beforeSave использовать класс
	 * Df_Admin_Model_Config_BackendChecker_CurrencyIsSupported
	 */
	extends Df_Admin_Model_Config_Backend {
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
	 * Этот метод вызывается из @see Df_Admin_Model_Config_BackendChecker::check() при провале проверки
	 * @override
	 * @param Exception $e
	 * @return void
	 */
	public function handleCheckerException(Exception $e) {
		// здесь надо отключить модуль "Яндекс.Маркет"
	}

	/**
	 * @overide
	 * @return Df_YandexMarket_Model_System_Config_Backend_Currency
	 */
	protected function _beforeSave() {
		parent::_beforeSave();
		if (
			!in_array(
				$this->getValue()
				,Df_YandexMarket_Model_System_Config_Source_Currency::getAllowedCurrencies()
			)
		) {
			/** @var string $currencyName */
			$currencyName = $this->getValue();
			try {
				$currencyName = df_zf_currency($this->getValue())->getName();
			}
			catch(Exception $e) {}
			df_error(
				rm_sprintf(
					'Яндекс.Маркет не допускает указанную Вами валюту «%s»'
					.' в качестве основной валюты магазина.'
					,$currencyName
				)
			);
		}
		else {
			/** @var Df_Admin_Model_Config_BackendChecker_CurrencyIsSupported $checker */
			$checker =
				Df_Admin_Model_Config_BackendChecker_CurrencyIsSupported::i($this->getValue(), $this)
			;
			$checker->check();
		}
		return $this;
	}

	const _CLASS = __CLASS__;
}