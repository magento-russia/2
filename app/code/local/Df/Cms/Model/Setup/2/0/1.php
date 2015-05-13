<?php
class Df_Cms_Model_Setup_2_0_1 extends Df_Core_Model_Setup {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		/** @var string $t_METADATA */
		$t_METADATA = rm_table('df_cms/hierarchy_metadata');
		/**
		 * Общая колонка для дополнительных настроек рубрики.
		 * Не выделяем для этих настроек отдельные колонки,
		 * потому что таких настроек может быть много,
		 * и их структура может часто меняться.
		 */
		$this->conn()->addColumn($t_METADATA, 'additional_settings', 'TEXT DEFAULT null');
		$this->conn()->dropColumn($t_METADATA, 'menu_visibility');
		$this->conn()->dropColumn($t_METADATA, 'menu_layout');
		rm_cache_clean();
	}

	/**
	 * @static
	 * @param Df_Core_Model_Resource_Setup $setup
	 * @return Df_Cms_Model_Setup_2_0_1
	 */
	public static function i(Df_Core_Model_Resource_Setup $setup) {return self::ic($setup, __CLASS__);}
}