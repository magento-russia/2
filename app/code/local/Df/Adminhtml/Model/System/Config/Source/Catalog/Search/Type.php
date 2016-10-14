<?php
class Df_Adminhtml_Model_System_Config_Source_Catalog_Search_Type
	extends  Mage_Adminhtml_Model_System_Config_Source_Catalog_Search_Type {
	/**
	 * Цель перекрытия —
	 * перевод названий вариантов поиска:
	 * «Like», «Fulltext», «Combine (Like and Fulltext)».
	 * @override
	 * @return array(array(string => string))
	 */
	public function toOptionArray() {
		/** @var array(array(string => string|mixed)) $result */
		$result = parent::toOptionArray();
		/** @var @var Df_CatalogSearch_Helper_Data $translator */
		$translator = Df_CatalogSearch_Helper_Data::s();
		foreach ($result as &$option) {
			/** @var array(string => string|mixed) $option */
			$option['label'] = $translator->__(dfa($option, 'label'));
		}
		return $result;
	}
}