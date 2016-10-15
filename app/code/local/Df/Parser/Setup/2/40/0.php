<?php
class Df_Parser_Setup_2_40_0 extends Df_Core_Setup {
	/**
	 * @override
	 * @see Df_Core_Setup::_process()
	 * @used-by Df_Core_Setup::process()
	 * @return void
	 */
	protected function _process() {
		df_remove_category_attribute(Df_Catalog_Model_Category::P__EXTERNAL_URL);
		df_eav_reset_categories();
	}
}