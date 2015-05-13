<?php
class Df_PageCache_Model_Resource_Crawler extends Mage_Core_Model_Mysql4_Abstract {
	/**
	 * Get statement for iterating store urls
	 * @param int $storeId
	 * @return Zend_Db_Statement
	 */
	public function getUrlStmt($storeId) {
		$select =
			$this->_getReadAdapter()->select()
				->from(
					rm_table('core/url_rewrite')
					, array('store_id', 'request_path')
				)
				->where('store_id=?', $storeId)
				->where('is_system=1')
		;
		$result = $this->_getReadAdapter()->query($select);
		return $result;
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
		$this->_init(self::TABLE_NAME, Df_Core_Model_Url_Rewrite::P__ID);
	}

	const _CLASS = __CLASS__;
	/**
  	 * Не ошибка!
 	 * Класс Df_PageCache_Model_Resource_Crawler действительно работает с таблицей core_url_rewrite
  	 */
	const TABLE_NAME = 'core/url_rewrite';
	/**
	 * @see Df_PageCache_Model_Crawler::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_PageCache_Model_Resource_Crawler */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}