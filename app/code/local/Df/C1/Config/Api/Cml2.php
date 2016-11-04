<?php
namespace Df\C1\Config\Api;
abstract class Cml2 extends \Df_Core_Model_Settings {
	/**
	 * @override
	 * @see Df_Core_Model_Settings::store()
	 * @used-by Df_Core_Model_Settings::_construct()
	 * @return \Df_Core_Model_StoreM
	 */
	protected function store() {return df_state()->getStoreProcessed();}
}