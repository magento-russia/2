<?php
/**
 * @method Df_Localization_Model_Onetime_Dictionary_Rule_Actions_Attribute getActions()
 * @method Df_Catalog_Model_Resource_Eav_Attribute getEntity()
 */
class Df_Localization_Model_Onetime_Processor_Attribute
	extends Df_Localization_Model_Onetime_Processor_Entity {
	/**
	 * @override
	 * @return string
	 */
	protected function getTitlePropertyName() {return df_should_not_be_here(__METHOD__);}

	/**
	 * @param Df_Localization_Model_Onetime_Dictionary_Term $term
	 * @return void
	 */
	protected function processTerm(Df_Localization_Model_Onetime_Dictionary_Term $term) {
		foreach (Mage::app()->getStores($withDefault = true) as $store) {
			/** @var Mage_Core_Model_Store $store */
			foreach ($this->getEntity()->getOptions($store) as $option) {
				/** @var Df_Eav_Model_Entity_Attribute_Option $option */
				/** @var string $labelProperty */
				/** @var string|null $textProcessed */
				/** @var string|null $value */
				$value = $option->getData('value');
				/** @var string|null $valueDefault */
				$valueDefault = $option->getData('default_value');
				if ((0 === intval($store->getId())) || ($value !== $valueDefault)) {
					$textProcessed = $this->translate($option->getData('value'), $term);
					if (!is_null($textProcessed)) {
						$this->updateOption($option, $store, $textProcessed);
					}
				}
			}
		}
	}

	/**
	 * @override
	 * @return void
	 */
	protected function updateTitle() {
		$this->getEntity()->setData('frontend_label', $this->getActions()->getTitleNew());
		/** @var array(int => string) $storeLabels */
		$storeLabels = $this->getEntity()->getStoreLabels();
		foreach (Mage::app()->getStores($withDefault = false) as $store) {
			/** @var Mage_Core_Model_Store $store */
			if ($this->getActions()->getTitleNewFrontend()) {
				$storeLabels[$store->getId()] = $this->getActions()->getTitleNewFrontend();
			}
			else {
				/** @var string|null $storeLabel */
				$storeLabel = df_a($storeLabels, $store->getId());
				if ($storeLabel) {
					$storeLabels[$store->getId()] = $this->getActions()->getTitleNew();
				}
			}
		}
		$this->getEntity()->setData('store_labels', $storeLabels);
	}

	/**
	 * @link http://www.webspeaks.in/2012/05/addupdate-attribute-option-values.html
	 * @param Df_Eav_Model_Entity_Attribute_Option $option
	 * @param Mage_Core_Model_Store $store
	 * @param string $newLabel
	 * @return void
	 */
	private function updateOption(
		Df_Eav_Model_Entity_Attribute_Option $option
		, Mage_Core_Model_Store $store
		, $newLabel
	) {
		/** @var array(string => array(int => array(int => string))) $propertyOption */
		$propertyOption = $this->getEntity()->getData('option');
		if (!is_array($propertyOption)) {
			$propertyOption = array();
		}
		/** @var array(int => array(int => string)) $propertyValue */
		$propertyValue = df_a($propertyOption, 'value', array());
		/** @var array(int => string) $propertyOptionId */
		$propertyOptionId =  df_a($propertyValue, $option->getId(), array());
		$propertyOptionId[$store->getId()] = $newLabel;
		$propertyValue[$option->getId()] = $propertyOptionId;
		$propertyOption['value'] = $propertyValue;
		$this->getEntity()->setData('option', $propertyOption);
	}
}


 