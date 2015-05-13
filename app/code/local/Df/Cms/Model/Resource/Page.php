<?php
class Df_Cms_Model_Resource_Page extends Mage_Cms_Model_Mysql4_Page {
	/**
	 * Возвращает идентификаторы страниц, не привязанных ни к одной из витрин.
	 * Метод выполнен по аналогии с методом @see Df_Cms_Model_Resource_Block::findOrphanBlockIds()
	 * @return int[]
	 */
	public function findOrphanIds() {
		return rm_conn()->fetchCol(
			rm_conn()->select()
				->from(array('p' => rm_table('cms_page')), 'page_id')
				->joinLeft(
					array('ps' => rm_table('cms_page_store'))
					,'p.page_id = ps.page_id'
					,array()
				)
				// Отфильтровываем страницы, которые привязаны к ранее удалённым витринам.
				->where(rm_conn()->prepareSqlCondition('ps.store_id', array(
					'nin' => array_keys(Mage::app()->getStores($withDefault = true, $codeKey = false))
				)))
				/**
				 * Это условие всё равно нужно,
				 * потому что условие выше говорит, каким не должно быть store_id у сирот,
				 * а данное условие, напротив, говорит, каким может быть store_id у сирот.
				 */
				->orWhere('ps.store_id IS NULL')
		);
	}

	/**
	 * Цель перекрытия —
	 * предоставление администратору возможности назначать самодельным страцам
	 * адреса с русскими (кириллическими) буквами.
	 * @override
	 * @param Mage_Core_Model_Abstract $object
	 * @return bool
	 */
	protected function isValidPageIdentifier(Mage_Core_Model_Abstract $object) {
		/** @var string */
		static $pattern = '/^[a-zа-яА-ЯёЁ0-9][a-zа-яА-ЯёЁ0-9_\/-]+(\.[a-zа-яА-ЯёЁ0-9_-]+)?$/u';
		return 1 === preg_match($pattern, $object->getData('identifier'));
	}

	const _CLASS = __CLASS__;
	/**
	 * @see Df_Cms_Model_Page::_construct()
	 * @see Df_Cms_Model_Resource_Page_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_Cms_Model_Resource_Page */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}