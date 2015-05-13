<?php
class Df_YandexMarket_Model_Setup_2_42_1 extends Df_Core_Model_Setup {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		/**
		 * 2014-12-16
		 * Между 2014-12-10 (версия 2.39.2) и 2014-12-15 (версия 2.42.0)
		 * класс @see Df_Catalog_Model_Processor_DeleteOrphanCategoryAttributesData
		 * работал некорректно: смотрите комментарий к методу
		 * @see Df_Catalog_Model_Processor_DeleteOrphanCategoryAttributesData::processInternal().
		 * Получалось, что при установке Российской сборки Magento
		 * товарные свойства модуля «Яндекс.Маркет» сначала добавлялись, а потом тут же удалялись?
		 * причём удалялись только из таблицы catalog/eav_attribute,
		 * оставаясь при этом в таблице eav/attribute.
		 *
		 * Нам нужно сейчас идентифицировать такую ситуацию:
		 * наличие информации о свойствах в таблице eav/attribute
		 * при отсутствии её в таблице catalog/eav_attribute,
		 * и в такой ситуации лучшим решением, видимо, будет поступить так:
		 * 1) удалить половинчатую информацию об этих свойствах из таблицы eav/attribute
		 * 2) заново выполнить установку данных свойств:
		 * @see Df_YandexMarket_Model_Setup_2_38_2::process()
		 */
		/**
		 * Отсутствие свойства в таблице catalog/eav_attribute
		 * является необходимым и достаточным критерием того,
		 * что это свойство нуждается в переустановке.
		 */
		/**
		 * Упрощаем алгоритм с учётом того,
		 * что если одно из товарных свойств модуля Яндекс.Маркет нуждается в переустановке,
		 * то и все они нуждаются в переустановке,
		 * потому что класс @see Df_Catalog_Model_Processor_DeleteOrphanCategoryAttributesData
		 * в дефектном состоянии сносил сразу все товарные свойства модуля Яндекс.Маркет:
		 * не могло быть так, что онт снёс одно, а оставил другое.
		 */
		if (
				false
			===
				rm_conn()->fetchOne(
					rm_conn()->select()
						->from(array('ea' => rm_table('eav/attribute')), 'ea.attribute_id')
						->joinInner(
							array('cea' => rm_table('catalog/eav_attribute'))
							, 'ea.attribute_id = cea.attribute_id'
						)
						->where('ea.attribute_code IN (?)', array(
							Df_YandexMarket_Const::ATTRIBUTE__CATEGORY
							, Df_YandexMarket_Const::ATTRIBUTE__SALES_NOTES
						))
				)
		) {
			rm_conn()->delete(
				rm_table('eav/attribute')
				,array('attribute_code IN (?)' => array(
					Df_YandexMarket_Const::ATTRIBUTE__CATEGORY
					, Df_YandexMarket_Const::ATTRIBUTE__SALES_NOTES
				))
			);
			Df_YandexMarket_Model_Setup_2_38_2::s()->process();
		}
	}

	/** @return Df_YandexMarket_Model_Setup_2_42_1 */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}