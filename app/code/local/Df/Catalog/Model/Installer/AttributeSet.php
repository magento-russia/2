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
			catch (Exception $e) {
				df_error('Не могу создать прикладной тип товара «%s».', $this->getName());
			}
			df_nat($result->getId());
			/**
			 * 2015-08-08
			 * Раньше здесь стоял код код:
					if (!is_null($this->getSkeletonId())) {
						$result->initFromSkeleton($this->getSkeletonId());
					}
					else {
						$this->addAttributesDefault($result);
					}
			 * Однако skeleton_id никто не задавал,
			 * и поэтому первая ветка if никогда не работала.
			 */
			$this->addAttributesDefault($result);
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
		/** @var array(string => array(string => string)) $attributes */
		$attributes = Df_Catalog_Model_Resource_Installer_Attribute::s()->defaultProductAttributes();
		foreach ($attributes as $attributeCode => $attributeData) {
			/** @var string $attributeCode */
			/** @var array $attributeData */
			df_assert_string_not_empty($attributeCode);
			df_assert_array($attributeData);
			/** @var string|null $groupName */
			$groupName = dfa($attributeData, 'group');
			if (!is_null($groupName)) {
				df_assert_string($groupName);
			}
			/** @var bool|null $isUserDefined */
			$isUserDefined = dfa($attributeData, 'user_defined');
			if (!is_null($isUserDefined)) {
				df_assert_boolean($isUserDefined);
			}
			/** @var int|null $sortOrder */
			$sortOrder = dfa($attributeData, 'sort_order');
			if (!is_null($sortOrder)) {
				df_assert_integer($sortOrder);
			}
			if ($groupName || !$isUserDefined) {
				Df_Catalog_Model_Installer_AddAttributeToSet::p(
					$attributeCode
					,$attributeSet->getId()
					,$groupName ? $groupName : self::GROUP_NAME__GENERAL
					,$sortOrder
				);
			}
		}
		// Здесь надо добавлять свои стандартные товарные свойства
		Df_Core_Setup_AttributeSet::runBlank($attributeSet);
		df_h()->eav()->packetUpdateEnd();
		rm_eav_reset();
	}

	/** @return string */
	private function getName() {return $this->cfg(self::$P__NAME);}

	/** @return string */
	private function needSkipReindexing() {return $this->cfg(self::$P__SKIP_REINDEXING, false);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__NAME, DF_V_STRING_NE)
			->_prop(self::$P__SKIP_REINDEXING, DF_V_BOOL)
		;
	}

	const GROUP_NAME__GENERAL = 'General';
	/** @var string */
	private static $P__NAME = 'name';
	/** @var string */
	private static $P__SKIP_REINDEXING = 'skip_reindexing';
	/**
	 * @static
	 * @used-by Df_1C_Cml2_Import_Data_Entity_Product::getAttributeSet()
	 * @used-by Lamoda_Catalog_Setup_Shoes::getAttributeSet()
	 * @param string $name
	 * @param bool $skipReindexing [optional]
	 * @return Df_Eav_Model_Entity_Attribute_Set
	 */
	public static function create($name, $skipReindexing = false) {
		$i = new self(array(self::$P__NAME => $name, self::$P__SKIP_REINDEXING => $skipReindexing));
		return $i->getResult();
	}
}