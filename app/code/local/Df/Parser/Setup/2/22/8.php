<?php
class Df_Parser_Setup_2_22_8 extends Df_Core_Setup {
	/**
	 * @override
	 * @see Df_Core_Setup::_process()
	 * @used-by Df_Core_Setup::process()
	 * @return void
	 */
	protected function _process() {
		rm_remove_category_attribute('lamoda__path');
		Df_Catalog_Model_Resource_Installer_Attribute::s()->addAdministrativeCategoryAttribute(
			$attributeId = Df_Catalog_Model_Category::P__EXTERNAL_URL
			,$attributeLabel = 'Веб-адрес на сайте-доноре'
		);
		rm_eav_reset_categories();
	}
}