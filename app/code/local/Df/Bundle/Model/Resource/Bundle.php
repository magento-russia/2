<?php
class Df_Bundle_Model_Resource_Bundle extends Mage_Bundle_Model_Mysql4_Bundle {
	/**
	 * @param int $productId
	 * @return Df_Bundle_Model_Resource_Bundle
	 */
	public function deleteAllOptions($productId) {
		df_param_integer($productId, 0);
		//$this->dropAllUnneededSelections($productId, array());
		/**
		 * При удалении опции все данные опции из других таблиц удаляться автоматически
		 * (ON DELETE CASCADE)
		 */
		$this->_getWriteAdapter()->delete(
			rm_table('bundle/option'), array('? = parent_id' => $productId)
		);
		return $this;
	}

	/** @return Df_Bundle_Model_Resource_Bundle */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}