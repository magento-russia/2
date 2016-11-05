<?php
class Df_Bundle_Model_Resource_Bundle extends Mage_Bundle_Model_Resource_Bundle {
	/**
	 * @param int $productId
	 * @return void
	 */
	public function deleteAllOptions($productId) {
		df_param_integer($productId, 0);
		//$this->dropAllUnneededSelections($productId, array());
		// При удалении опции все данные опции из других таблиц удаляться автоматически
		// (ON DELETE CASCADE)
		df_table_delete('bundle/option', 'parent_id', $productId);
	}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}