<?php
class Df_Tax_Model_Class_Source_Product extends Mage_Tax_Model_Class_Source_Product {
	/**
	 * 2015-04-11
	 * Цель перекрытия —
	 * фильтрация налоговых ставок по стране текущей витрины,
	 * чтобы при использовании налоговых ставок в выпадающих списках,
	 * (например, при назначении налоговой ставки товару)
	 * не показывать администраторам интернет-магазинам одной страны
	 * налоговые ставки других стран).
	 * @override
	 * @see Mage_Tax_Model_Class_Source_Product::getAllOptions()
	 * @used-by app/code/core/Mage/Tax/sql/tax_setup/install-1.6.0.0.php:
		$catalogInstaller->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'tax_class_id', array(
			(...)
			'source'                     => 'tax/class_source_product',
			(...)
		));
	 * @used-by Mage_Adminhtml_Model_System_Config_Source_Shipping_Taxclass::toOptionArray()
	 * @param bool $withEmpty [optional]
	 * @return array(array(string => string|int))
	 */
	public function getAllOptions($withEmpty = false) {
		if (is_null($this->_options)) {
			/** @var Df_Tax_Model_Resource_Class_Collection $c */
			$c = new Df_Tax_Model_Resource_Class_Collection;
			$c->addFieldToFilter('class_type', Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT);
			$c->filterByShopCountry();
			$this->_options = $c->toOptionArray();
		}
		/** @var array(array(string => string|int)) $result */
		$result = $this->_options;
		array_unshift($result, array('value' => '0', 'label' => Mage::helper('tax')->__('None')));
		if ($withEmpty) {
			array_unshift($result, array(
				'value' => '', 'label' => Mage::helper('tax')->__('-- Please Select --')
			));
		}
		return $result;
	}
}


