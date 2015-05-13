<?php
/**
 * Надо удостовериться, что в системе доступна требуемая валюта
 * и присутствует курс обмена учётной валюты магазина на требуемую валюту.
 */
class Df_Payment_Model_Config_Backend_Active_Currency extends Df_Admin_Model_Config_Backend {
	/**
	 * Этот метод вызывается из @see Df_Admin_Model_Config_BackendChecker::check() при провале проверки
	 * @override
	 * @param Exception $e
	 * @return void
	 */
	public function handleCheckerException(Exception $e) {
		// Надо явно отключить модуль, потому что возможна такая ситуация:
		// модуль был включён при корректных настройках валют,
		// затем настройки валют изменились на некорректные,
		// и затем администратор редактирует настройки модуля.
		// И вот тут-то надо не только оповестить администратора о некорректности настроек валют,
		// но и отключить модуль!
		$this->setValue(0);
	}

	/**
	 * @overide
	 * @return Df_YandexMarket_Model_System_Config_Backend_Category
	 */
	protected function _beforeSave() {
		try {
			// Выполняем проверки только при включенности модуля.
			if (rm_bool($this->getValue())) {
				$this->getFilterBeforeSave()->filter($this);
			}
		}
		catch(Exception $e) {
			/**
			 * Обратите внимание, что сюда мы попадаем только при системных сбоях,
			 * но не попадаем, если валюта не поддерживается,
			 * потому что внутри метода @see Df_Admin_Model_Config_BackendChecker::check()
			 * есть свой блок try... catch, и если валюта не поддерживается,
			 * то мы попадаем внутрь того catch, но не внутрь этого!
			 *
			 * Для обработки провала проверки используем метод
			 * @see Df_Payment_Model_Config_Backend_Active_Currency::handleCheckerException()
			 */
			rm_exception_to_session($e);
		}
		parent::_beforeSave();
	}

	/** @return Zend_Filter_Interface */
	private function createCurrencyIsSupportedFilter() {
		return Df_Core_Model_Filter_Adapter::i(
			Df_Admin_Model_Config_BackendChecker_CurrencyIsSupported::i($this->getCurrencyCode(), $this)
			,'check'
			,Df_Admin_Model_Config_BackendChecker_CurrencyIsSupported::P__BACKEND
		);
	}
	
	/** @return string */
	private function getCurrencyCode() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getFieldConfigParam('rm_currency');
		}
		return $this->{__METHOD__};
	}

	/** @return Zend_Filter */
	private function getFilterBeforeSave() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = new Zend_Filter();
			$this->{__METHOD__}->addFilter($this->createCurrencyIsSupportedFilter());
		}
		return $this->{__METHOD__};
	}
	const _CLASS = __CLASS__;
}