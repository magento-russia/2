<?php
class Df_YandexMarket_Setup_AttributeSet extends Df_Core_Setup_AttributeSet {
	/**
	 * @override
	 * @see Df_Core_Setup_AttributeSet::_process()
	 * @used-by Df_Core_Setup_AttributeSet::pc()
	 * @return void
	 */
	protected function _process() {
		/**
		 * Нужно:
		 * 1) добавить на административную карточку товара вкладку «Яндекс.Маркет»
		 * 2) Переместить (или добавить) на вкладку «Яндекс.Маркет» графу «Категория Яндекс.Маркета»
		 * 3) Добавить на вкладку «Яндекс.Маркет» графу «Примечание к товару (sales_notes)»
		 */
		foreach ($this->getAttributeMap() as $ordering => $attribute) {
			/** @var int $ordering */
			/** @var Mage_Eav_Model_Entity_Attribute $attribute */
			Df_Catalog_Model_Installer_AddAttributeToSet::p(
				$attribute->getAttributeCode()
				,$this->getAttributeSet()->getId()
				/**
				 * При отсутствии данной вкладки она добавляется автоматически:
				 * @see Df_Catalog_Model_Installer_AddAttributeToSet::process()
				 */
				,Df_YandexMarket_Const::PRODUCT_ATTRIBUTE_GROUP_NAME
				,$ordering
			);
		}
		rm_eav_reset();
	}

	/** @return Df_Catalog_Model_Resource_Eav_Attribute */
	private function getAttribute_Category() {
		return $this->getAttributeAdministrative(
			Df_YandexMarket_Const::ATTRIBUTE__CATEGORY
			, 'Категория Яндекс.Маркета'
			, self::$ORDERING__CATEGORY
			, array(
				'backend_model' => 'Df_YandexMarket_Model_Config_Backend_Category'
				,'note' =>
'Начните вводить первые символы — и система сама предложит Вам правильные варианты.
<br/>Указание категории упростит Яндекс.Маркету размещение Вашего товара в правильном разделе.'
			)
		);
	}

	/** @return Df_Catalog_Model_Resource_Eav_Attribute */
	private function getAttribute_SalesNotes() {
		return $this->getAttributeAdministrative(
			Df_YandexMarket_Const::ATTRIBUTE__SALES_NOTES
			, 'Примечание к товару (sales_notes)'
			, self::$ORDERING__SALES_NOTES
			,array(
				'frontend_class' => 'validate-length maximum-length-50'
				//,'frontend_input' => 'textarea'
				// Область действия значения свойства: «всеобщая», «витрина», «сайт»
				,'is_global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE
				,'note' =>
'Информация о минимальной сумме заказа,
партии товара или необходимости предоплаты, описания акций, скидок и распродаж.
<br/>Нельзя указывать информацию о доставке, гарантии, месте производства:
для этого предназначены другие поля.
<br/>До 50 символов.'
			)
		);
	}

	/**
	 * @param string $code
	 * @param string $label
	 * @param int $ordering
	 * @param array(string => string) $params [optional]
	 * @return Df_Catalog_Model_Resource_Eav_Attribute
	 */
	private function getAttributeAdministrative($code, $label, $ordering, array $params = array()) {
		if (!isset($this->{__METHOD__}[$code])) {
			$this->{__METHOD__}[$code] = rm_attributes()->createOrUpdate(
				Df_Catalog_Model_Attribute_Preset::administrative($params + array(
					// Код свойства
					'attribute_code' => $code
					// Класс объектов для свойства (товары, покупатели...)
					,'entity_type_id' => rm_eav_id_product()
					,'frontend_label' => $label
					,'sort_order' => $ordering
				))
			);
		}
		return $this->{__METHOD__}[$code];
	}

	/** @return Df_Catalog_Model_Resource_Eav_Attribute[] */
	private function getAttributeMap() {
		return array(
			self::$ORDERING__CATEGORY => $this->getAttribute_Category()
			,self::$ORDERING__SALES_NOTES => $this->getAttribute_SalesNotes()
		);
	}

	/** @var int */
	private static $ORDERING__CATEGORY = 1000;
	/** @var int */
	private static $ORDERING__SALES_NOTES = 2000;

	/**
	 * @return void
	 */
	public static function p() {
		foreach (df()->registry()->attributeSets() as $attributeSet) {
			/** Df_Eav_Model_Entity_Attribute_Set $attributeSet */
			self::pc(__CLASS__, $attributeSet);
		}
	}
}