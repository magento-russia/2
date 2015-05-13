<?php
class Df_Core_Helper_Request extends Mage_Core_Helper_Abstract {
	/**
	 * Этот метод был скопирован в ноябре 2012 года из
	 * @see Mage_Core_Controller_Varien_Action::_filterDates()
	 * @param array(string => string|mixed) $array
	 * @param string[] $dateFields
	 * @return array(string => string)
	 */
	public function filterDates(array $array, array $dateFields) {
		/** @var Zend_Filter_LocalizedToNormalized $filterInput */
		$filterInput = new Zend_Filter_LocalizedToNormalized(array('date_format' =>
			Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
		));
		/** @var Zend_Filter_NormalizedToLocalized $filterInternal */
		$filterInternal = new Zend_Filter_NormalizedToLocalized(array('date_format' =>
			Varien_Date::DATE_INTERNAL_FORMAT
		));
		foreach ($dateFields as $dateField) {
			/** @var string $dateField */
			if (array_key_exists($dateField, $array) && !empty($dateField)) {
				$array[$dateField] = $filterInternal->filter($filterInput->filter($array[$dateField]));
			}
		}
		return $array;
	}

	/**
	 * @param string $paramName
	 * @param mixed $default[optional]
	 * @return mixed
	 */
	public function getParam($paramName, $default = null) {
		return Mage::app()->getRequest()->getParam($paramName, $default);
	}

	/** @return Df_Core_Helper_Request */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}