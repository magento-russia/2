<?php
class Df_Catalog_Helper_Product extends Mage_Catalog_Helper_Data {
	/**
	 * @param int $attributeSetId
	 * @param string $groupName
	 * @param int|null $sortOrder [optional]
	 * @return Df_Catalog_Helper_Product
	 */
	public function addGroupToAttributeSetIfNeeded($attributeSetId, $groupName, $sortOrder = null) {
		df_param_integer($attributeSetId, 0);
		df_param_between($attributeSetId, 0, 1);
		df_param_string($groupName, 1);
		if (!isset($this->{__METHOD__}[$attributeSetId])) {
			$this->{__METHOD__}[$attributeSetId] = [];
		}
		if (!isset($this->{__METHOD__}[$attributeSetId][$groupName])) {
			Df_Catalog_Model_Resource_Installer_Attribute::s()
				->addAttributeGroup(
					df_eav_id_product()
					,$attributeSetId
					,$groupName
					,$sortOrder
				)
			;
			$this->{__METHOD__}[$attributeSetId][$groupName] = true;
			Df_Catalog_Model_Event_AttributeSet_GroupAdded::dispatch($attributeSetId, $groupName);
		}
		return $this;
	}

	/** @return Df_Eav_Model_Entity_Attribute_Set */
	public function getDefaultAttributeSet() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Eav_Model_Entity_Attribute_Set::ld(
					$this->getResource()->getEntityType()->getDefaultAttributeSetId()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $sku
	 * @return int|null
	 */
	public function getIdBySku($sku) {
		df_param_sku($sku, 0);
		/** @var int|null $result */
		$result = $this->getResource()->getIdBySku($sku);
		return $result ? (int)$result : null;
	}

	/**
	 * Работает быстрее, чем $product->getAttributeText(Df_Catalog_Model_Product::P__MANUFACTURER)
	 * @param string $manufacturerCode
	 * @return string|null
	 */
	public function getManufacturerNameByCode($manufacturerCode) {
		return dfa($this->getMapFromManufacturerCodeToName(), $manufacturerCode);
	}

	/** @return Df_Catalog_Model_Resource_Product */
	public function getResource() {return Df_Catalog_Model_Resource_Product::s();}

	/** @return Df_Catalog_Model_Product */
	public function getSingleton() {return Df_Catalog_Model_Product::s();}

	/** @return Mage_Catalog_Model_Product_Type_Configurable */
	public function getTypeConfigurable() {return Mage::getSingleton('catalog/product_type_configurable');}

	/**
	 * @param string $sku
	 * @return bool
	 */
	public function isExist($sku) {
		df_param_sku($sku, 0);
		return !!$this->getIdBySku($sku);
	}

	/** @return array(string => string) */
	private function getMapFromManufacturerCodeToName() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => string) $result */
			$result = [];
			/** @var Mage_Eav_Model_Entity_Attribute_Abstract|bool $manufacturerAttribute */
			$manufacturerAttribute =
				$this->getResource()->getAttribute(Df_Catalog_Model_Product::P__MANUFACTURER)
			;
			if ($manufacturerAttribute) {
				df_assert($manufacturerAttribute instanceof Mage_Eav_Model_Entity_Attribute_Abstract);
				/** @var Mage_Eav_Model_Entity_Attribute_Source_Interface $source */
				$source = $manufacturerAttribute->getSource();
				df_assert($source instanceof Mage_Eav_Model_Entity_Attribute_Source_Interface);
				$result = df_options_to_map($source->getAllOptions());
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}