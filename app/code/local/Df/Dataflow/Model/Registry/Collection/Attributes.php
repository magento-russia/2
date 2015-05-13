<?php
/**
 * @method Mage_Catalog_Model_Resource_Eav_Attribute|null findByLabel(string $label)
 */
class Df_Dataflow_Model_Registry_Collection_Attributes extends Df_Dataflow_Model_Registry_Collection {
	/**
	 * @override
	 * @param Mage_Core_Model_Abstract $entity
	 * @return void
	 */
	public function addEntity(Mage_Core_Model_Abstract $entity) {
		parent::addEntity($entity);
		/** @var Mage_Catalog_Model_Resource_Eav_Attribute $entity */
		$this->addEntityToCodeMap($entity);
	}

	/**
	 * @override
	 * @param string $code
	 * @return Mage_Catalog_Model_Resource_Eav_Attribute|null
	 */
	public function findByCode($code) {return df_a($this->getMapFromCodeToEntity(), $code);}

	/**
	 * @override
	 * @param string $code
	 * @param array $attributeData
	 * @return Mage_Catalog_Model_Resource_Eav_Attribute
	 */
	public function findByCodeOrCreate($code, array $attributeData) {
		df_param_string($code, 0);
		/** @var Mage_Catalog_Model_Resource_Eav_Attribute $result */
		$result = $this->findByCode($code);
		if (!is_null($result)) {
			$attributeData = array_merge($result->getData(), $attributeData);
		}
		$result =
			Df_Catalog_Model_Resource_Installer_AddAttribute::s()
				->addAttributeRm($code, $attributeData)
		;
		df_assert($result->getId());
		$this->addEntity($result);
		return $result;
	}

	/**
	 * @param Mage_Core_Model_Abstract $entity
	 * @return void
	 */
	public function removeEntity(Mage_Core_Model_Abstract $entity) {
		parent::removeEntity($entity);
		/** @var Mage_Catalog_Model_Resource_Eav_Attribute $entity */
		$this->removeEntityFromCodeMap($entity);
	}

	/**
	 * @override
	 * @return Df_Catalog_Model_Resource_Product_Attribute_Collection
	 */
	protected function createCollection() {
		/** @var Df_Catalog_Model_Resource_Product_Attribute_Collection $result */
		$result = Df_Catalog_Model_Resource_Product_Attribute_Collection::i();
		/**
		 * addFieldToSelect (Df_Eav_Const::ENTITY_EXTERNAL_ID)
		 * нам не нужно (ибо это поле — не из основной таблицы, а из дополнительной)
		 * и даже приводит к сбою (по той же причине)
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
	protected function getEntityClass() {return 'Mage_Catalog_Model_Resource_Eav_Attribute';}

	/**
	 * @override
	 * @param Mage_Core_Model_Abstract|Mage_Catalog_Model_Resource_Eav_Attribute $entity
	 * @return string|null
	 */
	protected function getEntityLabel(Mage_Core_Model_Abstract $entity) {
		return $entity->getFrontendLabel();
	}

	/**
	 * @param Mage_Catalog_Model_Resource_Eav_Attribute $entity
	 * @return void
	 */
	private function addEntityToCodeMap(Mage_Catalog_Model_Resource_Eav_Attribute $entity) {
		$this->getMapFromCodeToEntity();
		/** @var string $code */
		$code = $entity->getAttributeCode();
		df_assert_string_not_empty($code);
		$this->{__CLASS__ . '::getMapFromCodeToEntity'}[$code] = $entity;
	}	


	/** @return array(string => Mage_Catalog_Model_Resource_Eav_Attribute) */
	private function getMapFromCodeToEntity() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => Mage_Catalog_Model_Resource_Eav_Attribute) $result */
			$result = array();
			foreach ($this->getCollectionRm() as $entity) {
				/** @var Mage_Catalog_Model_Resource_Eav_Attribute $entity */
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
	 * @param Mage_Catalog_Model_Resource_Eav_Attribute $entity
	 * @return void
	 */
	private function removeEntityFromCodeMap(Mage_Catalog_Model_Resource_Eav_Attribute $entity) {
		$this->getMapFromCodeToEntity();
		/** @var string $code */
		$code = $entity->getAttributeCode();
		df_assert_string_not_empty($code);
		unset($this->{__CLASS__ . '::getMapFromCodeToEntity'}[$code]);
	}

	/** @return Df_Dataflow_Model_Registry_Collection_Attributes */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}