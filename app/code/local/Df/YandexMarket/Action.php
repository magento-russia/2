<?php
namespace Df\YandexMarket;
abstract class Action extends \Df_Core_Model_Action {
	/**
	 * @override
	 * @return bool
	 */
	protected function isModuleEnabledByAdmin() {return
		df_cfgr()->yandexMarket()->general()->isEnabled()
	;}
}