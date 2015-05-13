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
class Df_1C_Model_Setup_1_0_2 extends Df_Core_Model_Setup {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		/**
		 * Для товаров свойство «1С ID» добавляется методом
		 * @see Df_1C_Helper_Cml2_AttributeSet::addExternalIdToAttributeSet()
		 */
		$this->add1CIdToEntity('catalog_category', 'General Information');
		$this->add1CIdColumnToTable('eav/attribute_option');
		$this->add1CIdColumnToTable('catalog/eav_attribute');
		rm_cache_clean();
	}

	/**
	 * @param string $tablePlaceholder
	 * @return void
	 */
	private function add1CIdColumnToTable($tablePlaceholder) {
		df_param_string($tablePlaceholder, 0);
		/** @var string $tableName */
		$tableName = rm_table($tablePlaceholder);
		/**
		 * Обратите внимание, что напрямую писать {Df_Eav_Const::ENTITY_EXTERNAL_ID} нельзя:
		 * интерпретатор PHP не разбирает константы внутри {}.
		 * Поэтому заводим переменную.
		 */
		/** @var string $columnName */
		$columnName = Df_Eav_Const::ENTITY_EXTERNAL_ID;
		/** @var string $columnNameOld */
		$columnNameOld = Df_Eav_Const::ENTITY_EXTERNAL_ID_OLD;
		$this->runSilent("ALTER TABLE {$tableName} DROP COLUMN `{$columnNameOld}`;");
		$this->runSilent("ALTER TABLE {$tableName} DROP COLUMN `{$columnName}`;");
		$this->runSilent("
			ALTER TABLE {$tableName} ADD COLUMN `{$columnName}` VARCHAR(255) DEFAULT null;
		");
	}

	/**
	 * @param string $entityType
	 * @param string|null $groupName[optional]
	 * @param int $ordering[optional]
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
		if (self::attribute()->getAttributeId($entityType, Df_Eav_Const::ENTITY_EXTERNAL_ID_OLD)) {
			self::attribute()->removeAttribute($entityType, Df_Eav_Const::ENTITY_EXTERNAL_ID_OLD);
		}
		if (self::attribute()->getAttributeId($entityType, Df_Eav_Const::ENTITY_EXTERNAL_ID)) {
			self::attribute()->removeAttribute($entityType, Df_Eav_Const::ENTITY_EXTERNAL_ID);
		}
		/** @var int $entityTypeId */
		$entityTypeId = self::attribute()->getEntityTypeId($entityType);
		/** @var int $attributeSetId */
		$attributeSetId = self::attribute()->getDefaultAttributeSetId($entityTypeId);
		self::attribute()->addAttribute(
			$entityType
			,Df_Eav_Const::ENTITY_EXTERNAL_ID
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
			,Df_Eav_Const::ENTITY_EXTERNAL_ID
			,$ordering
		);
	}

	/**
	 * @static
	 * @param Df_Core_Model_Resource_Setup $setup
	 * @return Df_1C_Model_Setup_1_0_2
	 */
	public static function i(Df_Core_Model_Resource_Setup $setup) {return self::ic($setup, __CLASS__);}

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
			,'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL
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