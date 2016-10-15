<?php
class Df_Cms_Model_Resource_Page extends Mage_Cms_Model_Mysql4_Page {
	/**
	 * Возвращает идентификаторы страниц, не привязанных ни к одной из витрин.
	 * Метод выполнен по аналогии с методом @see Df_Cms_Model_Resource_Block::findOrphanBlockIds()
	 * @return int[]
	 */
	public function findOrphanIds() {
		return df_conn()->fetchCol(
			df_select()
				->from(array('p' => df_table('cms_page')), 'page_id')
				->joinLeft(
					array('ps' => df_table('cms_page_store'))
					, 'p.page_id = ps.page_id'
					, null
				)
				// Отфильтровываем страницы, которые привязаны к ранее удалённым витринам.
				->where(df_conn()->prepareSqlCondition('ps.store_id', array(
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



	/**
	 * 2015-02-09
	 * Возвращаем объект-одиночку именно таким способом,
	 * потому что наш класс перекрывает посредством <rewrite> системный класс,
	 * и мы хотим, чтобы вызов @see Mage::getResourceSingleton() ядром Magento
	 * возвращал тот же объект, что и наш метод @see s(),
	 * сохраняя тем самым объект одиночкой (это важно, например, для производительности:
	 * сохраняя объект одиночкой — мы сохраняем его кэш между всеми пользователями объекта).
	 * @return Df_Cms_Model_Resource_Page
	 */
	public static function s() {return Mage::getResourceSingleton('cms/page');}
}