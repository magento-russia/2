<?php
class Df_Seo_Model_Template_Adapter_Product extends Df_Seo_Model_Template_Adapter {
	/** @return Df_Catalog_Model_Product */
	public function getProduct() {return parent::getObject();}

	/**
	 * @param string $propertyName
	 * @return string
	 */
	protected function getPropertyClass($propertyName) {
		if (!isset($this->{__METHOD__}[$propertyName])) {
			$this->{__METHOD__}[$propertyName] = $this->evalPropertyClass($propertyName);
		}
		return $this->{__METHOD__}[$propertyName];
	}

	/**
	 * @param string $propertyName
	 * @return string
	 */
	private function evalPropertyClass($propertyName) {
		$node = $this->getConfigNode($propertyName);
		if (!$node) {
			$node = $this->getConfigNode('default');
		}
		return df_leaf_sne($node);
	}

	/**
	 * @param string $propertyType
	 * @return Mage_Core_Model_Config_Element
	*/
	private function getConfigNode($propertyType) {
		return df_leaf_child(
			rm_config_node('df/seo/template/objects', $this->getName(), 'properties'), $propertyType
		);
	}
	/** @used-by Df_Seo_Model_Template_Property_Product::_construct() */

}