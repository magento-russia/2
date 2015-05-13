<?php
class Df_Catalog_Helper_Product_Dataflow extends Mage_Catalog_Helper_Data {
	/** @return string[] */
	public function getIgnoredFields() {
		return $this->getFieldsByTag('ignore');
	}

	/** @return string[] */
	public function getInventoryFields() {
		if (!isset($this->_inventoryFields)) {
			/** @var string[] $result */
			$result = array();
			/** @var Mage_Core_Model_Config_Element[] $inventoryNodes */
			$inventoryNodes = $this->getNodesByTag('inventory');
			foreach ($inventoryNodes as $code => $inventoryNode) {
				/** @var string $code */
				/** @var Mage_Core_Model_Config_Element $inventoryNode */
				df_assert_string_not_empty($code);
				df_assert($inventoryNode instanceof Mage_Core_Model_Config_Element);
				$result[]= $code;
				if ($inventoryNode->is('use_config')) {
					$result[]= 'use_config_'.$code;
				}
			}
			df_result_array($result);
			$this->_inventoryFields = $result;
		}
		return $this->_inventoryFields;
	}
	/** @var string[] */
	private $_inventoryFields;

	/**
	 * @param string $productType
	 * @return string[]
	 */
	public function getInventoryFieldsByProductType($productType) {
		df_param_string($productType, 0);
		if (!isset($this->{__METHOD__}[$productType])) {
			/** @var string[] $result */
			$result = array();
			foreach ($this->getNodesByTag('inventory') as $code => $inventoryNode) {
				/** @var string $code */
				/** @var Mage_Core_Model_Config_Element $inventoryNode */
				/** @var SimpleXMLElement $productTypesNode */
				$productTypesNode = @$inventoryNode->product_type;
				df_assert($productTypesNode instanceof SimpleXMLElement);
				/** @var array $productTypes */
				$productTypes = array();
				foreach ($productTypesNode->children() as $productTypeNode) {
					/** @var SimpleXMLElement $productTypeNode */
					/** @var string $productTypeName */
					$productTypeName = $productTypeNode->getName();
					df_assert_string_not_empty($productTypeName);
					$productTypes[]= $productTypeName;
				}
				if (in_array($productType, $productTypes)) {
					$result[]= $code;
					if ($inventoryNode->is('ise_config')) {
						$result[]= 'use_config_' . $code;
					}
				}
			}
			$this->{__METHOD__}[$productType] = $result;
		}
		return $this->{__METHOD__}[$productType];
	}

	/** @return string[] */
	public function getNumericFields() {return $this->getFieldsByTag('to_number');}

	/** @return string[] */
	public function getRequiredFields() {return $this->getFieldsByTag('required');}

	/**
	 * @param string $tag
	 * @return string[]
	 */
	private function getFieldsByTag($tag) {
		df_assert_string($tag, 0);
		if (!isset($this->{__METHOD__}[$tag])) {
			$this->{__METHOD__}[$tag] = array_keys($this->getNodesByTag($tag));
		}
		return $this->{__METHOD__}[$tag];
	}

	/** @return SimpleXMLElement */
	private function getFieldset() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Mage::getConfig()->getFieldset('catalog_product_dataflow', 'admin');
			// Обратите внимание, что значение null мы не должны были получить,
			// потому что работаем с системными параметрами (они всегда должны быть)
			df_assert($this->{__METHOD__} instanceof SimpleXMLElement);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $tag
	 * @return Mage_Core_Model_Config_Element[]
	 */
	private function getNodesByTag($tag) {
		df_assert_string_not_empty($tag, 0);
		if (!isset($this->{__METHOD__}[$tag])) {
			/** @var Mage_Core_Model_Config_Element[] $result */
			$result = array();
			foreach ($this->getFieldset() as $code => $node) {
				/** @var string $code */
				/** @var Mage_Core_Model_Config_Element $node */
				df_assert_string_not_empty($code);
				df_assert($node instanceof Mage_Core_Model_Config_Element);
				if ($node->is($tag)) {
					$result[$code] = $node;
				}
			}
			$this->{__METHOD__}[$tag] = $result;
		}
		return $this->{__METHOD__}[$tag];
	}

	/** @return Df_Catalog_Helper_Product_Dataflow */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}