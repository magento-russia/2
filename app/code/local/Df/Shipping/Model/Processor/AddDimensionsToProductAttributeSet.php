<?php
class Df_Shipping_Model_Processor_AddDimensionsToProductAttributeSet
	extends Df_Core_Setup_AttributeSet {
	/**
	 * @override
	 * @see Df_Core_Setup_AttributeSet::_process()
	 * @used-by Df_Core_Setup_AttributeSet::pc()
	 * @return void
	 */
	protected function _process() {
		foreach ($this->getAttributeMap() as $ordering => $attribute) {
			/** @var int $ordering */
			/** @var Mage_Eav_Model_Entity_Attribute $attribute */
			Df_Catalog_Model_Installer_AddAttributeToSet::p(
				$attribute->getAttributeCode()
				,$this->getAttributeSet()->getId()
				,null
				,$ordering
			);
		}
	}

	/**
	 * @param string $code
	 * @param string $label
	 * @param int $ordering
	 * @return Df_Catalog_Model_Resource_Eav_Attribute
	 */
	private function getAttribute($code, $label, $ordering) {
		if (!isset($this->{__METHOD__}[$code])) {
			$this->{__METHOD__}[$code] = rm_attributes()->createOrUpdate(array(
				// Класс объектов для свойства (товары, покупатели...)
				'entity_type_id' => rm_eav_id_product()
				// Код свойства
				,'attribute_code' => $code
				// Область действия значения свойства: «всеобщая», «витрина», «сайт»
				,'is_global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL
				// Элемент управления для администратора
				,'frontend_input' => 'text'
				// Значение по умолчанию
				,'default_value' => null
				// Требовать ли от администратора
				// уникальности значения данного свойства для каждого товара?
				,'is_unique' => 0
				// Обязательно ли для заполнения?
				,'is_required' => 0
				// Административная проверка
				,'frontend_class' => 'validate-number validate-greater-than-zero'
				/**
				 * В Magento CE 1.4, если поле «attribute_model» присутствует,
				 * то его значение не может быть пустым
				 * @see Mage_Eav_Model_Config::_createAttribute()
				 */
				,'backend_model' => ''
				,'backend_type' => 'int'
				,'backend_table' => null
				,'frontend_model' => null
				,'frontend_label' => $label
				,'source_model' => ''
				,'is_user_defined' => 0
				// В Magento CE 1.4 значением поля «note» не может быть null
				,'note' => ''
				,'frontend_input_renderer' => null
				,'is_visible' => 1
				// Должна ли система учитывать
				// вхождения искомой посетителем магазина фразы
				// в значение данного свойства при полнотекстовом поиске?
				,'is_searchable' => 0
				// Использовать ли в качестве критерия расширенного поиска?
				,'is_visible_in_advanced_search' => 0
				// Показывать ли в таблице сравнения товаров?
				,'is_comparable' => 1
				// Использовать ли для пошаговой фильтрации?
				,'is_filterable' => 0
				// Использовать ли для пошаговой фильтрации результатов поиска?
				,'is_filterable_in_search' => 0
				// Позволять ли администратору использовать данное свойство
				// в качестве условия ценовых правил?
				,'is_used_for_promo_rules' => 0
				/**
				 * @deprecated
				 * Позволять ли администратору использовать данное свойство
				 * в качестве условия ценовых правил?
				 * (используется в CE Magento 1.4.0.1,
				 * но не используется в современных версиях Magento)
				 */
				,'is_used_for_price_rules' => 0
				// порядковый номер свойства в блоке пошаговой фильтрации
				,'position' => 0
				// Может ли значение содержать теги HTML?
				,'is_html_allowed_on_front' => 0
				// Показывать ли на витринной товарной карточке?
				,'is_visible_on_front' => 0
				,/**
				 * «Загружать ли данное свойство в товарные коллекции?»
				 * Включенность опции «used_in_product_listing»
				 * говорит системе загружать данное товарное свойство
				 * в коллекцию товаров при включенном режиме денормализации товаров.
				 * Думаю, стоит включить эту опцию.
				 * Обратите внимание, что до версии 2.16.2 эта опция была отключена,
				 * и сама по себе не включится у тех клиентов,
				 * кто устанавливал прежние версии Российской сборки Magento
				 */
				'used_in_product_listing' => 1
				// Давать ли покупателю возможность упорядочивать товары
				// по значениям данного свойства?
				,'used_for_sort_by' => 0
				// Позволять ли администратору использовать данное свойство
				// в качестве опции настраиваемого товара?
				,'is_configurable' => 0
				// Разрешать ли режим полного соответствия текстового редактора
				// при редактировании администратором значения данного свойства?
				,'is_wysiwyg_enabled' => 0
				,'sort_order' => $ordering
			));
		}
		return $this->{__METHOD__}[$code];
	}

	/** @return Df_Catalog_Model_Resource_Eav_Attribute */
	private function getAttributeHeight() {
		return $this->getAttribute(
			Df_Catalog_Model_Product::P__HEIGHT, 'Высота', self::$ORDERING_OFFSET + 2
		);
	}

	/** @return Df_Catalog_Model_Resource_Eav_Attribute */
	private function getAttributeLength() {
		return $this->getAttribute(
			Df_Catalog_Model_Product::P__LENGTH, 'Длина', self::$ORDERING_OFFSET
		);
	}

	/** @return Df_Catalog_Model_Resource_Eav_Attribute[] */
	private function getAttributeMap() {
		return array(
			self::$ORDERING_OFFSET => $this->getAttributeLength()
			,self::$ORDERING_OFFSET + 1 => $this->getAttributeWidth()
			,self::$ORDERING_OFFSET + 2 => $this->getAttributeHeight()
		);
	}

	/** @return Df_Catalog_Model_Resource_Eav_Attribute */
	private function getAttributeWidth() {
		return $this->getAttribute(
			Df_Catalog_Model_Product::P__WIDTH, 'Ширина', self::$ORDERING_OFFSET + 1
		);
	}

	/** @var int */
	private static $ORDERING_OFFSET = 100;

	/**
	 * @param Mage_Eav_Model_Entity_Attribute_Set $attributeSet
	 * @return void
	 */
	public static function process(Mage_Eav_Model_Entity_Attribute_Set $attributeSet) {
		self::pc(__CLASS__, $attributeSet);
	}
}