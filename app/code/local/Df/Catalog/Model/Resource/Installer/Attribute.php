<?php
class Df_Catalog_Model_Resource_Installer_Attribute extends Mage_Catalog_Model_Resource_Eav_Mysql4_Setup {
	/**
	 * @param string $attributeId
	 * @param string $attributeLabel
	 * @param string|null $groupName [optional]
	 * @param array(string => string|int|bool) $attributeCustomProperties [optional]
	 * @param int $ordering [optional]
	 * @return void
	 */
	public function addAdministrativeCategoryAttribute(
		$attributeId
		, $attributeLabel
		, $groupName = null
		, array $attributeCustomProperties = array()
		, $ordering = 10
	) {
		$this->addAdministrativeAttribute(
			'catalog_category'
			, $attributeId
			, $attributeLabel
			, $groupName ? $groupName : 'General Information'
			, $attributeCustomProperties
			, $ordering
		);
	}

	/**
	 * Этот метод отличается от родительского метода
	 * Mage_Eav_Model_Entity_Setup::addAttributeToSet
	 * только расширенной диагностикой
	 * (мы теперь знаем, что произошло в результате работы метода:
	 * действительно ли товарное свойство было добавлено к прикладному типу товаров,
	 * или же оно уже принадлежало этому прикладному типу,
	 * а если уже принадлежало — не сменалась ли его группа).
	 *
	 * @param mixed $entityTypeId
	 * @param mixed $setId
	 * @param mixed $groupId
	 * @param mixed $attributeId
	 * @param int $sortOrder
	 * @return string
  	 */
	public function addAttributeToSetRm(
		$entityTypeId
		,$setId
		,$groupId
		,$attributeId
		,$sortOrder = null
	) {
		/** @var string $result */
		$result = self::ADD_ATTRIBUTE_TO_SET__NOT_CHANGED;
		$entityTypeId = $this->getEntityTypeId($entityTypeId);
		$setId = $this->getAttributeSetId($entityTypeId, $setId);
		$groupId = $this->getAttributeGroupId($entityTypeId, $setId, $groupId);
		$attributeId = $this->getAttributeId($entityTypeId, $attributeId);
		$table = rm_table('eav/entity_attribute');
		$bind = array(
			'attribute_set_id' => $setId
			, 'attribute_id' => $attributeId
		);
		$select =
			$this->_conn->select()
				->from($table)
				->where('attribute_set_id = :attribute_set_id')
				->where('attribute_id = :attribute_id')
		;
		$row = $this->_conn->fetchRow($select, $bind);
		if ($row) {
			if ($row['attribute_group_id'] != $groupId) {
				$where = array('entity_attribute_id =?' => $row['entity_attribute_id']);
				$data  = array('attribute_group_id' => $groupId);
				$this->_conn->update($table, $data, $where);
				$result = self::ADD_ATTRIBUTE_TO_SET__CHANGED_GROUP;
			}
		}
		else {
			$data =
				array(
					'entity_type_id' => $entityTypeId,'attribute_set_id' => $setId,'attribute_group_id' => $groupId,'attribute_id' => $attributeId,'sort_order' =>
						$this->getAttributeSortOrder(
							$entityTypeId
							,$setId
							,$groupId
							,$sortOrder
						)
				)
			;
			$this->_conn->insert($table, $data);
			$result = self::ADD_ATTRIBUTE_TO_SET__ADDED;
		}
		/**
		 * Вот в таких ситуациях, когда у нас меняется структура прикладного типа товаров,
		 * нам нужно сбросить глобальный кэш EAV.
		 */
		rm_eav_reset();
		return $result;
	}
	
	/** @return int */
	public function getCategoryAttributeSetId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				self::s()->getDefaultAttributeSetId(
					self::s()->getEntityTypeId(
						Mage_Catalog_Model_Category::ENTITY
					)
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getGeneralGroupName() {return $this->_generalGroupName;}

	/**
	 * @override
	 * @return Df_Catalog_Model_Resource_Installer_Attribute
	 */
	public function startSetup() {
		parent::startSetup();
		Df_Core_Bootstrap::s()->init();
		Df_Zf_Bootstrap::s()->init();
		return $this;
	}

	/** @return Df_Catalog_Model_Resource_Installer_Attribute */
	public function cleanQueryCache() {
		$this->_setupCache = array();
		return $this;
	}

	/**
	 * Вообще говоря, этот метод способен работать как с товарными разделами, так и с товарами
	 * (в зависимости от значения параметра $entityType).
	 * Однако 2014-09-29 я намеренно сделал данный метод приватным
	 * и тем самым ограничил его применение только товарными разделами
	 * (это класс сам использует данный метод только для товарных разделов,
	 * а другие классы использовать приватный метод этого класса не могут).
	 *
	 * Проблема с товарами заключалась в том, что данный метод
	 * добавляет свойство сразу ко всем текущим прикладным типам товара,
	 * но никак не решает задачу добавления этого свойства
	 * к программно создаваемым в будущем прикладным типам товара
	 * (программно типы товара создают на 2014-09-29 модули 1С и МойСклад).
	 *
	 * Обратите внимание, что создаваемых вручную администратором прикладных типов товара
	 * эта проблема не касалась, потому что вручную прикладные типы
	 * всегда создаются на основе какого-либо уже существующего прикладного типа
	 * и наследуют все свойства этого прикладного типа
	 * (в том числе и добавленные нами свойства).
	 *
	 * Поэтому правильное решение для товаров
	 * смотрите у наследников класса @see Df_Core_Model_Setup_AttributeSet:
	 * @see Df_Shipping_Model_Processor_AddDimensionsToProductAttributeSet
	 * @see Df_YandexMarket_Model_Setup_Processor_AttributeSet
	 * В целом, оно заключается в загрузке/создании/изменении свойства
	 * вызовом @see Df_Dataflow_Model_Registry_Collection_Attributes::findByCodeOrCreate()
	 * (df()->registry()->attributes()->findByCodeOrCreate())
	 * и затем ручной привязкой свойства к конкретному прикладному типу товаров,
	 * а когда надо привязать свойство сразу ко всем прикладным типам товаров,
	 * то это делается в цикле с вызовом соответствующего настройщика прикладного типа товаров.
	 * Смотрите, например, @see Df_YandexMarket_Model_Setup_2_38_2::process():
		foreach (df()->registry()->attributeSets() as $attributeSet) {
			Df_YandexMarket_Model_Setup_Processor_AttributeSet::process($attributeSet);
		}
	 *
	 * @param string $entityType
	 * @param string $attributeId
	 * @param string $attributeLabel
	 * @param string|null $groupName [optional]
	 * @param array(string => string|int|bool) $attributeCustomProperties [optional]
	 * @param int $ordering [optional]
	 * @return void
	 */
	private function addAdministrativeAttribute(
		$entityType
		, $attributeId
		, $attributeLabel
		, $groupName = null
		, array $attributeCustomProperties = array()
		, $ordering = 10
	) {
		df_param_string($entityType, 0);
		if (is_null($groupName)) {
			$groupName = $this->getGeneralGroupName();
		}
		df_param_string($groupName, 1);
		df_param_integer($ordering, 2);
		$this->cleanCache();
		if ($this->getAttributeId($entityType, $attributeId)) {
			$this->removeAttribute($entityType, $attributeId);
		}
		/** @var int $entityTypeId */
		$entityTypeId = $this->getEntityTypeId($entityType);
		/** @var int $attributeSetId */
		$attributeSetId = $this->getDefaultAttributeSetId($entityTypeId);
		$this->addAttribute(
			$entityType
			, $attributeId
			, array_merge(
				Df_Catalog_Model_Attribute_Preset::administrative()
				,array('label' => $attributeLabel)
				,$attributeCustomProperties
			)
		);
		$this->addAttributeToGroup(
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
			,$attributeId
			,$ordering
		);
	}

	const _CLASS = __CLASS__;
	const ADD_ATTRIBUTE_TO_SET__ADDED = 'added';
	const ADD_ATTRIBUTE_TO_SET__NOT_CHANGED = 'not_changed';
	const ADD_ATTRIBUTE_TO_SET__CHANGED_GROUP = 'changed_group';

	/** @return Df_Catalog_Model_Resource_Installer_Attribute */
	public static function s() {static $r; return $r ? $r : $r = new self('df_catalog_setup');}
}