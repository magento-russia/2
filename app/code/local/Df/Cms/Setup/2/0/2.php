<?php
class Df_Cms_Setup_2_0_2 extends Df_Core_Setup {
	/**
	 * @override
	 * @see Df_Core_Setup::_process()
	 * @used-by Df_Core_Setup::process()
	 * @return void
	 */
	protected function _process() {
		$this->conn()->modifyColumn(
			rm_table('cms/page'), 'website_root', "tinyint(1) NOT null default '0'"
		);
	}
}