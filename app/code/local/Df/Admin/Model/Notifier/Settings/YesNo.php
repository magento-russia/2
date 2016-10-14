<?php
abstract class Df_Admin_Model_Notifier_Settings_YesNo extends Df_Admin_Model_Notifier_Settings {
	/** @return string */
	abstract protected function getConfigPath();

	/**
	 * @override
	 * @param Df_Core_Model_StoreM $store
	 * @return bool
	 */
	protected function isStoreAffected(Df_Core_Model_StoreM $store) {
		return !Mage::getStoreConfigFlag($this->getConfigPath(), $store);
	}
}