<?php
namespace Df\Payment\Config;
class Manager extends ManagerBase {
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