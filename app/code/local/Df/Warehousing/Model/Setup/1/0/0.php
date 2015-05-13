<?php
class Df_Warehousing_Model_Setup_1_0_0 extends Df_Core_Model_Setup {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		Df_Warehousing_Model_Resource_Warehouse::s()->tableCreate($this->getSetup());
	}

	/**
	 * @static
	 * @param Df_Core_Model_Resource_Setup $setup
	 * @return Df_Warehousing_Model_Setup_1_0_0
	 */
	public static function i(Df_Core_Model_Resource_Setup $setup) {return self::ic($setup, __CLASS__);}
}