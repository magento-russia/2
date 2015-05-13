<?php
class Df_Banner_Model_Setup_0_1_2 extends Df_Core_Model_Setup {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		/** @var string $t_DF_BANNER_ITEM */
		$t_DF_BANNER_ITEM = rm_table('df_banner_item');
		$this->getSetup()->run("ALTER TABLE {$t_DF_BANNER_ITEM} ADD `banner_order` int(11) default 0;");
	}

	/**
	 * @static
	 * @param Df_Core_Model_Resource_Setup $setup
	 * @return Df_Banner_Model_Setup_0_1_2
	 */
	public static function i(Df_Core_Model_Resource_Setup $setup) {return self::ic($setup, __CLASS__);}
}