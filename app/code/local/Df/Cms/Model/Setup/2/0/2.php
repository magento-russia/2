<?php
class Df_Cms_Model_Setup_2_0_2 extends Df_Core_Model_Setup {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		$this->conn()->modifyColumn(
			rm_table('cms/page'), 'website_root', "tinyint(1) NOT null default '0'"
		);
		rm_cache_clean();
	}

	/**
	 * @static
	 * @param Df_Core_Model_Resource_Setup $setup
	 * @return Df_Cms_Model_Setup_2_0_2
	 */
	public static function i(Df_Core_Model_Resource_Setup $setup) {return self::ic($setup, __CLASS__);}
}