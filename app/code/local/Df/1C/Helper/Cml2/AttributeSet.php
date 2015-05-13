<?php
class Df_1C_Helper_Cml2_AttributeSet extends Mage_Core_Helper_Data {
	/**
	 * @param int $attributeSetId
	 * @return Df_1C_Helper_Cml2_AttributeSet
	 */
	public function addExternalIdToAttributeSet($attributeSetId) {
		df_param_integer($attributeSetId, 0);
		df_param_between($attributeSetId, 0, 1);
		/** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
		$attribute =
			df()->registry()->attributes()->findByCodeOrCreate(
				Df_Eav_Const::ENTITY_EXTERNAL_ID
				,array(
					'entity_type_id' => rm_eav_id_product()
					,'attribute_code' => Df_Eav_Const::ENTITY_EXTERNAL_ID
					/**
					 * В Magento CE 1.4, если поле «attribute_model» присутствует,
					 * то его значение не может быть пустым
					 * @see Mage_Eav_Model_Config::_createAttribute
					 */
					,'backend_model' => ''
					,'backend_type' => 'varchar'
					,'backend_table' => null
					,'frontend_model' => null
					,'frontend_input' => 'text'
					,'frontend_label' => '1С ID'
					,'frontend_class' => null
					,'source_model' => ''
					,'is_required' => 0
					,'is_user_defined' => 0
					,'default_value' => null
					,'is_unique' => 0
					// В Magento CE 1.4 значением поля «note» не может быть null
					,'note' => ''
					,'frontend_input_renderer' => null
					,'is_global' => 1
					,'is_visible' => 1
					,'is_searchable' => 1
					,'is_filterable' => 1
					,'is_comparable' => 1
					,'is_visible_on_front' => 0
					,'is_html_allowed_on_front' => 0
					,'is_used_for_price_rules' => 0
					,'is_filterable_in_search' => 1
					,/**
					 * Включенность опции «used_in_product_listing»
					 * говорит системе загружать данное товарное свойство
					 * в коллекцию товаров при включенном режиме денормализации товаров.
					 *
					 * Для свойства Df_Eav_Const::ENTITY_EXTERNAL_ID
					 * эта опция обязательно должна быть включена.
					 */
					'used_in_product_listing' => 1
					,'used_for_sort_by' => 0
					,'is_configurable' => 1
					,'is_visible_in_advanced_search' => 1
					,'position' => 0
					,'is_wysiwyg_enabled' => 0
					,'is_used_for_promo_rules' => 0
				)
				,100
			)
		;
		df_h()->_1c()->create1CAttributeGroupIfNeeded($attributeSetId);
		Df_Catalog_Model_Installer_AddAttributeToSet::processStatic(
			$attributeCode = $attribute->getAttributeCode()
			,$attributeSetId
			,$groupName = Df_1C_Const::PRODUCT_ATTRIBUTE_GROUP_NAME
			,$ordering = 100
		);
		return $this;
	}

	/** @return Df_1C_Helper_Cml2_AttributeSet */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}