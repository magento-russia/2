<?php
class Df_Cms_Setup_2_0_1 extends Df_Core_Setup {
	/**
	 * @override
	 * @see Df_Core_Setup::_process()
	 * @used-by Df_Core_Setup::process()
	 * @return void
	 */
	protected function _process() {
		/** @var string $t_METADATA */
		$t_METADATA = rm_table(Df_Cms_Model_Resource_Hierarchy_Node::TABLE_META_DATA);
		// Общая колонка для дополнительных настроек рубрики.
		// Не выделяем для этих настроек отдельные колонки,
		// потому что таких настроек может быть много,
		// и их структура может часто меняться.
		$this->conn()->addColumn($t_METADATA, 'additional_settings', 'TEXT DEFAULT null');
		$this->conn()->dropColumn($t_METADATA, 'menu_visibility');
		$this->conn()->dropColumn($t_METADATA, 'menu_layout');
	}
}