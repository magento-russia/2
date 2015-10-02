<?php
class Df_Catalog_Model_Attribute_Preset extends Df_Core_Model_Abstract {
	/**
	 * @param array(string => string|int|bool) $params [optional]
	 * @return array(string => string|int|bool)
	 */
	public static function administrative(array $params = array()) {
		return self::preset($params, array(
			/**
			 * В Magento CE 1.4, если поле «attribute_model» присутствует,
			 * то его значение не может быть пустым
			 * @see Mage_Eav_Model_Config::_createAttribute()
			 */
			//'attribute_model' => 'eav/entity_attribute',
			'backend_model' => ''
			,'backend_table' => null
			,'backend_type' => 'varchar'
			// Значение по умолчанию
			,'default_value' => ''
			// Административная проверка
			,'frontend_class' => ''
			// Элемент управления для администратора
			,'frontend_input' => 'text'
			,'frontend_input_renderer' => null
			//,'frontend_label' => 'Примечание к товару (sales_notes)'
			,'frontend_model' => null
			// Показывать ли в таблице сравнения товаров?
			,'is_comparable' => 0
			// Позволять ли администратору использовать данное свойство
			// в качестве опции настраиваемого товара?
			,'is_configurable' => 0
			// Использовать ли для пошаговой фильтрации?
			,'is_filterable' => 0
			// Использовать ли для пошаговой фильтрации результатов поиска?
			,'is_filterable_in_search' => 0
			// Область действия значения свойства: «всеобщая», «витрина», «сайт»
			,'is_global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL
			// Может ли значение содержать теги HTML?
			,'is_html_allowed_on_front' => 0
			// Обязательно ли для заполнения?
			,'is_required' => 0
			// Должна ли система учитывать
			// вхождения искомой посетителем магазина фразы
			// в значение данного свойства при полнотекстовом поиске?
			,'is_searchable' => 0
			// Требовать ли от администратора
			// уникальности значения данного свойства для каждого товара?
			,'is_unique' => 0
			// Позволять ли администратору использовать данное свойство
			// в качестве условия ценовых правил?
			,'is_used_for_promo_rules' => 0
			/**
			 * Сможет ли администратор удалять данное свойство.
			 * обратите внимание, что данный массив одновременно содержит ключи
			 * user_defined и is_user_defined,
			 * потому что ключ user_defined используется методом
			 * @see Mage_Eav_Model_Entity_Setup::addAttribute(),
			 * а ключ is_user_defined хоть и заполняется автоматически при вызове метода
			 * @see Mage_Eav_Model_Entity_Setup::_prepareValues(),
			 * однако у класса @see Df_Catalog_Model_Resource_Installer_AddAttribute
			 * (потомка Mage_Eav_Model_Entity_Setup)
			 * метод @see Df_Catalog_Model_Resource_Installer_AddAttribute::_prepareValues()
			 * отключен.
			 */
			,'is_user_defined' => 0
			,'is_visible' => 1
			// Использовать ли в качестве критерия расширенного поиска?
			,'is_visible_in_advanced_search' => 0
			// Показывать ли на витринной товарной карточке?
			,'is_visible_on_front' => 0
			// Разрешать ли режим полного соответствия текстового редактора
			// при редактировании администратором значения данного свойства?
			,'is_wysiwyg_enabled' => 0
			// В Magento CE 1.4 значением поля «note» не может быть null
			,'note' => ''
			// Порядковый номер свойства в блоке пошаговой фильтрации
			,'position' => 0
			,'source_model' => ''
			/**
			 * @deprecated
			 * Позволять ли администратору использовать данное свойство
			 * в качестве условия ценовых правил?
			 * (используется в Magento 1.4.0.1,
			 * не используется в современных версиях Magento)
			 */
			,'is_used_for_price_rules' => 0
			// Давать ли покупателю возможность упорядочивать товары
			// по значениям данного свойства?
			,'used_for_sort_by' => 0
			/**
			 * «Загружать ли в товарные коллекции?»
			 * Включенность опции «used_in_product_listing»
			 * говорит системе загружать данное товарное свойство
			 * в коллекцию товаров при включенном режиме денормализации товаров.
			 * Думаю, стоит включить эту опцию.
			 * Обратите внимание, что до версии 2.16.2 эта опция была отключена,
			 * и сама по себе не включится у тех клиентов,
			 * кто устанавливал прежние версии Российской сборки Magento.
			 * Обратите внимание, что название этой опции
			 * почему-то не содержит приставку «is_-».
			 */
			,'used_in_product_listing' => 1
			/**
			 * Сможет ли администратор удалять данное свойство.
			 * обратите внимание, что данный массив одновременно содержит ключи
			 * user_defined и is_user_defined,
			 * потому что ключ user_defined используется методом
			 * @see Mage_Eav_Model_Entity_Setup::addAttribute(),
			 * а ключ is_user_defined хоть и заполняется автоматически при вызове метода
			 * @see Mage_Eav_Model_Entity_Setup::_prepareValues(),
			 * однако у класса @see Df_Catalog_Model_Resource_Installer_AddAttribute
			 * (потомка Mage_Eav_Model_Entity_Setup)
			 * метод @see Df_Catalog_Model_Resource_Installer_AddAttribute::_prepareValues()
			 * отключен.
			 */
			,'user_defined' => 0
		));
	}

	/**
	 * @param array(string => string) $params
	 * @param array(string => string) $defaults
	 * @return array(string => string)
	 */
	private static function preset(array $params, array $defaults) {
		return array_merge($defaults, $params);
	}
}