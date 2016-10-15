<?php
/**
 * Родительский класс — именно @see Mage_Catalog_Model_Resource_Eav_Attribute даже в Magento CE 1.4
 * @method string|null getFrontendInput()
 * @method bool|null getIsRequired()
 * @method string|null getNote()
 * @method bool hasAttributeSetInfo()
 */
class Df_Catalog_Model_Resource_Eav_Attribute extends Mage_Catalog_Model_Resource_Eav_Attribute {
	/** @return string|null */
	public function get1CId() {return $this->_getData(Df_1C_Const::ENTITY_EXTERNAL_ID);}

	/**
	 * 2015-01-24
	 * Обратите внимание, что этот метод возвращает опции
	 * только для тех свойств, чьи опции перечислены на вкладке «Опции»
	 * административного экрана свойства.
	 * У таких свойств поле «source_model» «имеет значение eav/entity_attribute_source_table»
	 * Его же возвращает и метод @see getSourceModel()
	 *
	 * Однако есть и другие свойства (например, «Страна производства» [country_of_manufacture]),
	 * у которых вкладка «Опции» — пустая,
	 * а перечень значений берётся каким-нибудь нестандартным способом,
	 * запрограммированным классом указанным в поле «source_model».
	 * В частности, у свойства «Страна производства» [country_of_manufacture]
	 * поле «source_model» имеет значение «catalog/product_attribute_source_countryofmanufacture»,
	 * и опции берутся из справочника стран.
	 * Для таких опций наш метод @see getOptions() неприменим.
	 *
	 * @param Df_Core_Model_StoreM|int|string|bool|null $store  [optional]
	 * @return Df_Eav_Model_Resource_Entity_Attribute_Option_Collection
	 */
	public function getOptions($store = null) {
		$store = df_store(is_null($store) ? $this->getStoreId() : $store);
		if (!isset($this->_options[$store->getId()])) {
			/** @var Df_Eav_Model_Resource_Entity_Attribute_Option_Collection $result */
			$result = Df_Eav_Model_Entity_Attribute_Option::c();
			$result->setPositionOrder('asc');
			$result->setAttributeFilter($this->getId());
			$result->setStoreFilter($store->getId());
			Df_Varien_Data_Collection::unsetDataChanges($result);
			$this->_options[$store->getId()] = $result;
		}
		return $this->_options[$store->getId()];
	}

	/**
	 * @override
	 * @return Df_Catalog_Model_Resource_Product_Attribute_Collection
	 */
	public function getResourceCollection() {return self::c();}

	/**
	 * По аналогии с @see Df_Catalog_Model_Product::getTitle()
	 * @return string
	 */
	public function getTitle() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = strtr('«{label}» [{code}]', array(
				'{label}' => $this->getFrontendLabel(), '{code}' => $this->getName()
			));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string|string[] $type
	 * @return bool
	 */
	public function isApplicableToProductSystemType($type) {
		/** @var bool $result */
		/** @var string[] $applyTo */
		$applyTo = $this->getApplyTo();
		return !$applyTo || array_intersect($applyTo, df_array($type));
	}
	
	/**
	 * @param string $attributeCode
	 * @param string|string[] $options
	 * @return Df_Catalog_Model_Resource_Eav_Attribute
	 */
	public static function addOptions($attributeCode, $options) {
		/** @var Df_Catalog_Model_Resource_Eav_Attribute $attribute */
		$attribute = df_attributes()->findByCode($attributeCode);
		return df_attributes()->createOrUpdate(
			array_merge($attribute->getData(), array('option' =>
				Df_Eav_Model_Entity_Attribute_Option_Calculator::calculateStatic(
					$attribute, df_array($options), $isModeInsert = true, $caseInsensitive = true
				)
			))
		);
	}

	/**
	 * @override
	 * @return Df_Catalog_Model_Resource_Attribute
	 * 2016-10-14
	 * В родительском классе метод переобъявлен через PHPDoc,
	 * и поэтому среда разработки думает, что он публичен.
	 */
	/** @noinspection PhpHierarchyChecksInspection */
	protected function _getResource() {return Df_Catalog_Model_Resource_Attribute::s();}

	/** @var array(int => Df_Eav_Model_Resource_Entity_Attribute_Option_Collection) */
	private $_options = array();

	/**
	 * @used-by Df_1C_Cml2_Export_Processor_Catalog_Attribute_Real::_construct()
	 * @used-by Df_1C_Cml2_Import_Data_Entity_OfferPart_OptionValue_Empty::_construct()
	 * @used-by Df_Catalog_Model_Resource_Product_Attribute_Collection::_construct()
	 * @used-by Df_Dataflow_Model_Registry_Collection_Attributes::getEntityClass()
	 * @used-by Df_Localization_Onetime_Dictionary_Rule_Conditions_Attribute::getEntityClass()
	 */


	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Catalog_Model_Resource_Eav_Attribute
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/** @return Df_Catalog_Model_Resource_Product_Attribute_Collection */
	public static function c() {return new Df_Catalog_Model_Resource_Product_Attribute_Collection;}
}

