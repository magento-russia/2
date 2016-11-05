<?php
/**
 * @method Df_Catalog_Model_Resource_Eav_Attribute|null findByLabel(string $label)
 */
class Df_Dataflow_Model_Registry_Collection_Attributes extends Df_Dataflow_Model_Registry_Collection {
	/**
	 * @override
	 * @param Mage_Core_Model_Abstract|Df_Catalog_Model_Resource_Eav_Attribute $entity
	 * @return void
	 */
	public function addEntity(Mage_Core_Model_Abstract $entity) {
		parent::addEntity($entity);
		/** @var Df_Catalog_Model_Resource_Eav_Attribute $entity */
		$this->addEntityToCodeMap($entity);
	}

	/**
	 * Обратите внимание на универсальность этого метода:
	 * он используется как для создания, так и для изменения свойств.
	 * @override
	 * @param array(string => mixed) $attributeData
	 * @param string|null $code [optional]
	 * @return Df_Catalog_Model_Resource_Eav_Attribute
	 */
	public function createOrUpdate(array $attributeData, $code = null) {
		if ($code) {
			$attributeData['attribute_code'] = $code;
		}
		$code = dfa($attributeData, 'attribute_code');
		df_assert_string_not_empty($code);
		/** @var Df_Catalog_Model_Resource_Eav_Attribute $result */
		$result = $this->findByCode($code);
		if (!is_null($result)) {
			$attributeData = $attributeData + $result->getData();
		}
		$result = Df_Catalog_Model_Resource_Installer_AddAttribute::s()->addAttributeRm(
			$code, $attributeData
		);
		df_assert($result->getId());
		$this->addEntity($result);
		return $result;
	}

	/**
	 * @override
	 * @param string $code
	 * @return Df_Catalog_Model_Resource_Eav_Attribute|null
	 */
	public function findByCode($code) {return dfa($this->getMapFromCodeToEntity(), $code);}

	/**
	 * @param Mage_Core_Model_Abstract|Df_Catalog_Model_Resource_Eav_Attribute $entity
	 * @return void
	 */
	public function removeEntity(Mage_Core_Model_Abstract $entity) {
		parent::removeEntity($entity);
		$this->removeEntityFromCodeMap($entity);
	}

	/**
	 * @override
	 * @return Df_Catalog_Model_Resource_Product_Attribute_Collection
	 */
	protected function createCollection() {
		/** @var Df_Catalog_Model_Resource_Product_Attribute_Collection $result */
		$result = Df_Catalog_Model_Resource_Eav_Attribute::c();
		$result->addSetInfo(true);
		/**
		 * Вызывать
		 * @see Df_Catalog_Model_Resource_Product_Attribute_Collection::addFieldToSelect(
		  		\Df\C1\C::ENTITY_EXTERNAL_ID
		  )
		 * здесь ошибочно (ибо это поле — не из основной таблицы,
		 * а из дополнительной таблицы «catalog/eav_attribute»,
		 * @see Df_C1_Setup_1_0_2::process())
		 * и даже приводит к сбою (по той же причине).
		 * Данные из дополнительной таблицы «catalog/eav_attribute»
		 * добавляются к коллекции автоматически:
		 * @see Mage_Catalog_Model_Resource_Product_Attribute_Collection::_initSelect()
		 */
		/**
		 * Пока используем это вместо $result->addHasOptionsFilter(),
		 * потому что addHasOptionsFilter отбраковывает пустые справочники
		 */
		//$result->setFrontendInputTypeFilter('select');
		return $result;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getEntityClass() {return Df_Catalog_Model_Resource_Eav_Attribute::class;}

	/**
	 * @override
	 * @param Mage_Core_Model_Abstract|Df_Catalog_Model_Resource_Eav_Attribute $entity
	 * @return string|null
	 */
	protected function getEntityLabel(Mage_Core_Model_Abstract $entity) {
		return $entity->getFrontendLabel();
	}

	/**
	 * @param Df_Catalog_Model_Resource_Eav_Attribute $entity
	 * @return void
	 */
	private function addEntityToCodeMap(Df_Catalog_Model_Resource_Eav_Attribute $entity) {
		$this->getMapFromCodeToEntity();
		/** @var string $code */
		$code = $entity->getAttributeCode();
		df_assert_string_not_empty($code);
		$this->{__CLASS__ . '::getMapFromCodeToEntity'}[$code] = $entity;
	}	


	/** @return array(string => Df_Catalog_Model_Resource_Eav_Attribute) */
	private function getMapFromCodeToEntity() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => Df_Catalog_Model_Resource_Eav_Attribute) $result */
			$result = [];
			foreach ($this->getCollectionRm() as $entity) {
				/** @var Df_Catalog_Model_Resource_Eav_Attribute $entity */
				/** @var string|null $code */
				$code = $entity->getAttributeCode();
				if ($code) {
					df_assert_string($code);
					$result[$code] = $entity;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param Df_Catalog_Model_Resource_Eav_Attribute $entity
	 * @return void
	 */
	private function removeEntityFromCodeMap(Df_Catalog_Model_Resource_Eav_Attribute $entity) {
		$this->getMapFromCodeToEntity();
		/** @var string $code */
		$code = $entity->getAttributeCode();
		df_assert_string_not_empty($code);
		unset($this->{__CLASS__ . '::getMapFromCodeToEntity'}[$code]);
	}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}