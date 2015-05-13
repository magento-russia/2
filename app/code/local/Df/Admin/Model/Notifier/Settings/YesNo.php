<?php
abstract class Df_Admin_Model_Notifier_Settings_YesNo extends Df_Admin_Model_Notifier_Settings {
	/** @return string */
	abstract protected function getConfigPath();

	/**
	 * @override
	 * @param Mage_Core_Model_Store $store
	 * @return bool
	 */
	protected function isStoreAffected(Mage_Core_Model_Store $store) {
		return !Mage::getStoreConfigFlag($this->getConfigPath(), $store);
	}
}