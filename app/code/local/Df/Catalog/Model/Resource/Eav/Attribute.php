<?php
/** Родительский класс — именно @see Mage_Catalog_Model_Resource_Eav_Attribute даже в Magento CE 1.4 */
class Df_Catalog_Model_Resource_Eav_Attribute extends Mage_Catalog_Model_Resource_Eav_Attribute {
	/**
	 * @param Mage_Core_Model_Store|null $store
	 * @return Df_Eav_Model_Resource_Entity_Attribute_Option_Collection
	 */
	public function getOptions($store = null) {
		if (!$store) {
			$store = Mage::app()->getStore($this->getStoreId());
		}
		if (!isset($this->_options[$store->getId()])) {
			/** @var Df_Eav_Model_Resource_Entity_Attribute_Option_Collection $result */
			$result = Df_Eav_Model_Resource_Entity_Attribute_Option_Collection::i();
			$result->setAttributeFilter($this->getId());
			$result->setStoreFilter($store->getId());
			Df_Varien_Data_Collection::unsetDataChanges($result);
			$this->_options[$store->getId()] = $result;
		}
		return $this->_options[$store->getId()];
	}
	/** @var array(int => Df_Eav_Model_Resource_Entity_Attribute_Option_Collection) */
	private $_options = array();
	
	/**
	 * @param string $attributeCode
	 * @param string|string[] $options
	 * @return Mage_Catalog_Model_Resource_Eav_Attribute
	 */
	public static function addOptions($attributeCode, $options) {
		if (!is_array($options)) {
			$options = array($options);
		}
		/** @var Mage_Catalog_Model_Resource_Eav_Attribute $result */
		$result = df()->registry()->attributes()->findByCode($attributeCode);
		/** @var array(string => mixed) $attributeData */
		$attributeData =
			array_merge(
				$result->getData()
				,array(
					'option' =>
						Df_Eav_Model_Entity_Attribute_Option_Calculator
							::calculateStatic(
								$result
								,$options
								,$isModeInsert = true
								,$caseInsensitive = true
							)
				)
			)
		;
		$result = df()->registry()->attributes()->findByCodeOrCreate($attributeCode, $attributeData);
		return $result;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Catalog_Model_Resource_Attribute::mf());
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Catalog_Model_Resource_Eav_Attribute
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/** @return string */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
}

