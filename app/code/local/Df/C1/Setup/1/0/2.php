<?php
/**
 * Соответствие версий инсталлятора версиям Российской сборки:
 * 1.0.0  => 1.47.0
 * 1.0.1  => 1.47.12
 * 1.0.2  => 1.47.13
 *
 * Версии инсталлятора 1.0.0 и 1.0.1 удалил 2014-09-28 как устаревшие
 * и никому из клиентов больше не нужные.
 */
class Df_C1_Setup_1_0_2 extends Df_C1_Setup {
	/**
	 * Для товаров свойство «1С ID» добавляется функцией @see df_1c_add_external_id_attribute_to_set()
	 * @override
	 * @see Df_Core_Setup::_process()
	 * @used-by Df_Core_Setup::process()
	 * @return void
	 */
	protected function _process() {
		$this->add1CIdToEntity('catalog_category', 'General Information');
		df_eav_reset_categories();
		$this->add1CIdColumnToTable('eav/attribute_option');
		$this->add1CIdColumnToTable('catalog/eav_attribute');
	}

	/**
	 * @param string $entityType
	 * @param string|null $groupNamg [optional]
	 * @param int $ordering [optional]
	 * @return void
	 */
	private function add1CIdToEntity($entityType, $groupName = null, $ordering = 10) {
		df_param_string($entityType, 0);
		if (is_null($groupName)) {
			$groupName = self::attribute()->getGeneralGroupName();
		}
		df_param_string($groupName, 1);
		df_param_integer($ordering, 2);
		self::attribute()->cleanCache();
		df_remove_attribute($entityType, Df_C1_Const::ENTITY_EXTERNAL_ID_OLD);
		df_remove_attribute($entityType, Df_C1_Const::ENTITY_EXTERNAL_ID);
		/** @var int $entityTypeId */
		$entityTypeId = self::attribute()->getEntityTypeId($entityType);
		/** @var int $attributeSetId */
		$attributeSetId = self::attribute()->getDefaultAttributeSetId($entityTypeId);
		self::attribute()->addAttribute(
			$entityType
			,Df_C1_Const::ENTITY_EXTERNAL_ID
			,self::get1CIdProperties()
		);
		self::attribute()->addAttributeToGroup(
			$entityTypeId
			,$attributeSetId
			/**
			 * Не используем синтаксис
			 * $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId)
			 * потому что он при предварительно включенной русификации
			 * может приводить к созданию дополнительной вкладки ("Основное")
			 * вместо размещения свойства на главной вкладке ("Главное").
			 */
			,$groupName
			,Df_C1_Const::ENTITY_EXTERNAL_ID
			,$ordering
		);
	}

	/** @return array(string => string) */
	private static function get1CIdProperties() {
		return array(
			'type' => 'varchar'
			,'backend' => ''
			,'frontend' => ''
			,'label' => '1С ID'
			,'input' => 'text'
			,'class' => ''
			,'source' => ''
			,'global' => Df_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL
			,'visible' => true
			,'required' => false
			,'user_defined' => false
			,'default' => ''
			,'searchable' => false
			,'filterable' => false
			,'comparable' => false
			,'visible_on_front' => false
			,'unique' => false
		);
	}
}