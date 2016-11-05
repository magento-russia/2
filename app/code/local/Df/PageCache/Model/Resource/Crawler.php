<?php
class Df_PageCache_Model_Resource_Crawler extends Df_Core_Model_Resource {
	/**
	 * Get statement for iterating store urls
	 * @param int $storeId
	 * @return Zend_Db_Statement
	 */
	public function getUrlStmt($storeId) {
		return df_conn()->query(df_select()
			->from(df_table(Df_Catalog_Model_Resource_Url::TABLE), array('store_id', 'request_path'))
			->where('? = store_id', $storeId)
			->where('1 = is_system'))
		;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		// Родительский конструктор вызывать нельзя,
		// потому что он — абстрактный.
		// Не ошибка!
		// Эта ресурсная модель действительно работает с таблицей core_url_rewrite
		$this->_init(Df_Catalog_Model_Resource_Url::TABLE, Df_Core_Model_Url_Rewrite::P__ID);
	}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}