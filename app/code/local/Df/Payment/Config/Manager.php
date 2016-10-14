<?php
class Df_Payment_Config_Manager extends Df_Payment_Config_ManagerBase {
	/**
	 * @override
	 * @param string $key
	 * @return string|null
	 */
	protected function _getValue($key) {return $this->store()->getConfig($key);}

	/**
	 * @override
	 * @return string
	 */
	protected function getKeyBase() {return 'df_payment';}
}