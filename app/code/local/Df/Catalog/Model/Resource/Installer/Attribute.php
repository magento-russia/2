<?php
class Df_Catalog_Model_Resource_Installer_Attribute extends Mage_Catalog_Model_Resource_Setup {
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
		$table = df_table('eav/entity_attribute');
		$bind = array(
			'attribute_set_id' => $setId
			, 'attribute_id' => $attributeId
		);
		$select = df_select()
			->from($table, array('attribute_group_id', 'entity_attribute_id'))
			->where('attribute_set_id = :attribute_set_id')
			->where('attribute_id = :attribute_id')
		;
		$row = df_conn()->fetchRow($select, $bind);
		if ($row) {
			if ($row['attribute_group_id'] != $groupId) {
				df_conn()->update(
					$table
					, array('attribute_group_id' => $groupId)
					, array('? = entity_attribute_id ' => $row['entity_attribute_id'])
				);
				$result = self::ADD_ATTRIBUTE_TO_SET__CHANGED_GROUP;
				df_eav_reset();
			}
		}
		else {
			df_conn()->insert($table, array(
				'entity_type_id' => $entityTypeId
				,'attribute_set_id' => $setId
				,'attribute_group_id' => $groupId
				,'attribute_id' => $attributeId
				,'sort_order' => $this->getAttributeSortOrder(
					$entityTypeId, $setId, $groupId, $sortOrder
				)
			));
			$result = self::ADD_ATTRIBUTE_TO_SET__ADDED;
			df_eav_reset();
		}
		return $result;
	}

	/**
	 * 2015-08-09
	 * @return array(string => array(string => string))
	 */
	public function defaultProductAttributes() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				dfa(dfa($this->getDefaultEntities(), 'catalog_product'), 'attributes')
				/**
				 * 2015-08-09
				 * Родительский метод @uses Mage_Catalog_Model_Resource_Setup::getDefaultEntities()
				 * не добавляет свойство для налоговогой группы товара.
				 * Это свойство добавляет модуль Mage_Tax в своём установщике:
				 * https://github.com/OpenMage/magento-mirror/blob/1.9.2.1/app/code/core/Mage/Tax/sql/tax_setup/install-1.6.0.0.php#L263-L285
				 * Скопировал оттуда код для добавления.
				 */
				+ array('tax_class_id' => array(
					'group' => 'Prices'
					,'type' => 'int'
					,'backend' => ''
					,'frontend' => ''
					,'label' => 'Tax Class'
					,'input' => 'select'
					,'class' => ''
					,'source' => 'tax/class_source_product'
					,'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE
					,'visible' => true
					,'required' => true
					,'user_defined' => false
					,'default' => ''
					,'searchable' => true
					,'filterable' => false
					,'comparable' => false
					,'visible_on_front' => false
					,'visible_in_advanced_search' => true
					,'used_in_product_listing' => true
					,'unique' => false
					,'apply_to' => 'simple,configurable,virtual,downloadable,bundle'
				))
			;
		}
		return $this->{__METHOD__};
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
		Df_Core_Boot::run();
		return $this;
	}

	/** @return Df_Catalog_Model_Resource_Installer_Attribute */
	public function cleanQueryCache() {
		$this->_setupCache = [];
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
	 * смотрите у наследников класса @see Df_Core_Setup_AttributeSet:
	 * @see \Df\Shipping\Processor\AddDimensionsToProductAttributeSet
	 * @see \Df\YandexMarket\Setup\AttributeSet
	 * В целом, оно заключается в загрузке/создании/изменении свойства
	 * вызовом @see Df_Dataflow_Model_Registry_Collection_Attributes::createOrUpdate()
	 * (df_attributes()->createOrUpdate())
	 * и затем ручной привязкой свойства к конкретному прикладному типу товаров,
	 * а когда надо привязать свойство сразу ко всем прикладным типам товаров,
	 * то это делается в цикле с вызовом соответствующего настройщика прикладного типа товаров.
	 * Смотрите, например, @see \Df\YandexMarket\Setup\AttributeSet::p():
		foreach (df()->registry()->attributeSets() as $attributeSet) {
			\Df\YandexMarket\Setup\AttributeSet::process($attributeSet);
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
		/**
		 * 2015-03-13
		 * Обратите внимание, что метод @uses Mage_Eav_Model_Entity_Setup::removeAttribute()
		 * сам проверяет, присутствует ли свойство, и выполняет работу только при наличии свойства,
		 * поэтому вручную проверять присутствие свойства не нужно.
		 * @see df_remove_attribute()
		 */
		$this->removeAttribute($entityType, $attributeId);
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


	const ADD_ATTRIBUTE_TO_SET__ADDED = 'added';
	const ADD_ATTRIBUTE_TO_SET__NOT_CHANGED = 'not_changed';
	const ADD_ATTRIBUTE_TO_SET__CHANGED_GROUP = 'changed_group';

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self('df_catalog_setup');}
}