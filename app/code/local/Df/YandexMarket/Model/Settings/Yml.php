<?php
abstract class Df_YandexMarket_Model_Settings_Yml extends Df_Core_Model_Settings {
	/**
	 * @override
	 * @return Mage_Core_Model_Store
	 */
	protected function getStore() {return rm_state()->getStoreProcessed();}
}