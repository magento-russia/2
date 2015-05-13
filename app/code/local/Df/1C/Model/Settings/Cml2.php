<?php
abstract class Df_1C_Model_Settings_Cml2 extends Df_Core_Model_Settings {
	/**
	 * @override
	 * @return Mage_Core_Model_Store
	 */
	protected function getStore() {return rm_state()->getStoreProcessed();}
	const _CLASS = __CLASS__;
}