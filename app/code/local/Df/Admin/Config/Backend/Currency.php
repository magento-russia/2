<?php
/**
 * Надо удостовериться, что в системе доступна требуемая валюта
 * и присутствует курс обмена учётной валюты магазина на требуемую валюту.
 */
class Df_Admin_Config_Backend_Currency extends Df_Admin_Config_Backend {
	/**
	 * @used-by Df_Admin_Config_BackendChecker::check() при провале проверки
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
	 * @return Df_Admin_Config_Backend_Currency
	 */
	protected function _beforeSave() {
		// Выполняем проверки только при включенности модуля.
		if (df_bool($this->getValue())) {
			Df_Admin_Config_BackendChecker_CurrencyIsSupported::_check($this, $this->getValue());
		}
		parent::_beforeSave();
	}
}