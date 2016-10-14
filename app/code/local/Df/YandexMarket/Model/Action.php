<?php
abstract class Df_YandexMarket_Model_Action extends Df_Core_Model_Action {
	/**
	 * @override
	 * @return bool
	 */
	protected function isModuleEnabledByAdmin() {
		return df_cfg()->yandexMarket()->general()->isEnabled();
	}
}