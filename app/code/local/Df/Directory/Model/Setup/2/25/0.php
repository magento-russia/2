<?php
class Df_Directory_Model_Setup_2_25_0 extends Df_Core_Model_Setup {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		Df_Directory_Model_Setup_Processor_InstallRegions_Kazakhstan::i($this->getSetup())->process();
	}

	/**
	 * @static
	 * @param Df_Core_Model_Resource_Setup $setup
	 * @return Df_Directory_Model_Setup_2_25_0
	 */
	public static function i(Df_Core_Model_Resource_Setup $setup) {return self::ic($setup, __CLASS__);}
}