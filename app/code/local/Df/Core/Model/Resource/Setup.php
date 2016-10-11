<?php
/**
 * Обратите внимание, что родительский класс — именно @see Mage_Core_Model_Resource_Setup
 * даже в Magento CE 1.4.
 * Также обратите внимание, что родительский класс не наследуется ни от какого другого класса
 * (ни от Mage_Core_Model_Abstract, ни от Varien_Object).
 */
class Df_Core_Model_Resource_Setup extends Mage_Core_Model_Resource_Setup {
	/**
	 * @override
	 * @return Df_Core_Model_Resource_Setup
	 */
	public function startSetup() {
		parent::startSetup();
		Df_Core_Boot::run();
		return $this;
	}

	const _CLASS = __CLASS__;
	/** @return Df_Core_Model_Resource_Setup */
	public static function s() {static $r; return $r ? $r : $r = new self('df_core_setup');}
}