<?php
class Df_Banner_Setup_0_1_2 extends Df_Core_Setup {
	/**
	 * @override
	 * @see Df_Core_Setup::_process()
	 * @used-by Df_Core_Setup::process()
	 * @return void
	 */
	protected function _process() {
		$t_DF_BANNER_ITEM = df_table(Df_Banner_Model_Resource_Banneritem::TABLE);
		$this->run("ALTER TABLE {$t_DF_BANNER_ITEM} ADD `banner_order` int(11) default 0;");
	}
}