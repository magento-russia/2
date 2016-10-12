<?php
class Df_Catalog_Model_Installer_AttributeSet extends Df_Core_Model {
	/** @return Df_Eav_Model_Entity_Attribute_Set */
	public function getResult() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Eav_Model_Entity_Attribute_Set $result */
			$result = Df_Eav_Model_Entity_Attribute_Set::i();
			$result
				->setEntityTypeId(rm_eav_id_product())
				->setAttributeSetName($this->getName())
				->validate()
			;
			try {
				$result->save();
			}
			catch(Exception $e) {
				df_error('Не могу создать прикладной тип товара «%s».', $this->getName());
			}
			rm_nat($result->getId());
			if (!is_null($this->getSkeletonId())) {
				$result->initFromSkeleton($this->getSkeletonId());
			}
			else {
				$this->addAttributesDefault($result);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param Mage_Eav_Model_Entity_Attribute_Set $attributeSet
	 * @return void
	 */
	private function addAttributesDefault(Mage_Eav_Model_Entity_Attribute_Set $attributeSet) {
		df_h()->eav()->packetUpdateBegin();
		foreach ($this->getAttributesDefaultData() as $attributeCode => $attributeData) {
			/** @var string $attributeCode */
			/** @var array $attributeData */
			df_assert_string_not_empty($attributeCode);
			df_assert_array($attributeData);
			/** @var string|null $groupName */
			$groupName = df_a($attributeData, 'group');
			if (!is_null($groupName)) {
				df_assert_string($groupName);
			}
			/** @var bool|null $isUserDefined */
			$isUserDefined = df_a($attributeData, 'user_defined');
			if (!is_null($isUserDefined)) {
				df_assert_boolean($isUserDefined);
			}
			/** @var int|null $sortOrder */
			$sortOrder = df_a($attributeData, 'sort_order');
			if (!is_null($sortOrder)) {
				df_assert_integer($sortOrder);
			}
			if ($groupName || !$isUserDefined) {
				Df_Catalog_Model_Installer_AddAttributeToSet::processStatic(
					$attributeCode
					,$attributeSet->getId()
					,$groupName ? $groupName : self::GROUP_NAME__GENERAL
					,$sortOrder
				);
			}
		}
		df_h()->eav()->packetUpdateEnd();
		// Здесь надо добавлять свои стандартные товарные свойства
		$this->runBlankAttributeSetProcessors($attributeSet);
	}

	/** @return array */
	private function getAttributesDefaultData() {
		/** @var array $result */
		$result =
			df_a(
				df_a(
					Df_Catalog_Model_Resource_Installer_Attribute::s()->getDefaultEntities()
					,'catalog_product'
				)
				,'attributes'
			)
		;
		df_result_array($result);
		return $result;
	}

	/** @return string */
	private function getName() {return $this->cfg(self::P__NAME);}

	/** @return int|null */
	private function getSkeletonId() {return $this->cfg(self::P__SKELETON_ID);}

	/** @return string */
	private function needSkipReindexing() {return $this->cfg(self::P__SKIP_REINDEXING, false);}

	/**
	 * @param Mage_Eav_Model_Entity_Attribute_Set $attributeSet
	 * @return void
	 */
	private function runBlankAttributeSetProcessors(Mage_Eav_Model_Entity_Attribute_Set $attributeSet) {
		foreach (Df_Core_Model_Config::s()->getStringNodes('df/attribute_set_processors') as $class) {
			/** @var string $class */
			Df_Core_Model_Setup_AttributeSet::processByClass($class, $attributeSet);
		}
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__NAME, self::V_STRING_NE)
			->_prop(self::P__SKIP_REINDEXING, self::V_BOOL)
			->_prop(self::P__SKELETON_ID, self::V_INT, false)
		;
	}
	const _CLASS = __CLASS__;
	const GROUP_NAME__GENERAL = 'General';
	const P__NAME = 'name';
	const P__SKIP_REINDEXING = 'skip_reindexing';
	const P__SKELETON_ID = 'skeleton_id';
	/**
	 * @static
	 * @param string $name
	 * @param int|null $skeletonId [optional]
	 * @return Df_Eav_Model_Entity_Attribute_Set
	 */
	public static function create($name, $skeletonId = null) {
		return self::i(array(self::P__NAME => $name, self::P__SKELETON_ID => $skeletonId))->getResult();
	}
	/**
	 * @static
	 * @param array(string => mixed) $parameters
	 * @return Df_Catalog_Model_Installer_AttributeSet
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}