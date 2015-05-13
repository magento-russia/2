<?php
class Df_CatalogRule_Model_Rule_Condition_Product extends Mage_CatalogRule_Model_Rule_Condition_Product {
	/**
	 * Цель перекрытия —
	 * устранение дефекта Magento CE 1.8:
	 * ценовые правила для каталога с условиями, основанными на товарных свойствах
	 * с глобальной областью доступности, работают неправильно.
	 * @link https://www.google.com/search?q=Magento+1.8+rule+not+working
	 * @link http://stackoverflow.com/a/19976036/254475
	 * @link https://bitbucket.org/gferon/magento-1.8-catalogrule-fix/
	 * @link http://www.magentocommerce.com/bug-tracking/issue?issue=15936
	 * @link http://www.magentocommerce.com/bug-tracking/issue?issue=15896
	 * @link http://www.magentocommerce.com/bug-tracking/issue?issue=15075
	 *
	 * @override
	 * @param Varien_Object $object
	 * @return mixed
	 */
	protected function _getAttributeValue($object) {
		/** @var bool $patchNeeded */
		static $patchNeeded;
		if (!isset($patchNeeded)) {
			$patchNeeded = df_magento_version('1.8.0.0', '1.8.1.0');
		}
		/** @var mixed $result */
		if (!$patchNeeded) {
			$result = parent::_getAttributeValue($object);
		}
		else {
			/** @var int $storeId */
			$storeId = $object->getDataUsingMethod('store_id');
			/** @var int $defaultStoreId */
			$defaultStoreId = Mage_Core_Model_App::ADMIN_STORE_ID;
			/** @var array(int => mixed) $productValues */
			$productValues =
				isset($this->_entityAttributeValues[$object->getId()])
				? $this->_entityAttributeValues[$object->getId()]
				: array($defaultStoreId => $object->getData($this->getAttribute()))
			;
			/** @var mixed $defaultValue */
			$defaultValue =
				isset($productValues[$defaultStoreId])
				? $productValues[$defaultStoreId]
				: null;
			$result =
				isset($productValues[$storeId])
				? $productValues[$storeId]
				: $defaultValue
			;
			$result = $this->_prepareDatetimeValue($result, $object);
			$result = $this->_prepareMultiselectValue($result, $object);
		}
		return $result;
	}
}