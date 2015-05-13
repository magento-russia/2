<?php
class Df_Shipping_Helper_Data extends Mage_Core_Helper_Abstract {
	/**
	 * @param mixed $value
	 * @return Df_Shipping_Helper_Data
	 */
	public function assertPostalCodeDestination($value) {
		df_assert($value, 'Укажите почтовый индекс адреса доставки.');
		df_param_string($value, 0);
		/** @var Zend_Validate_PostCode $validator */
		$validator = new Zend_Validate_PostCode(array('locale' => Df_Core_Const::LOCALE__RUSSIAN));
		if (!$validator->isValid($value)) {
			df_error('Система не понимает указанный Вами почтовый индекс адреса доставки: «%s».', $value);
		}
		return $this;
	}

	/**
	 * @param mixed $value
	 * @return Df_Shipping_Helper_Data
	 */
	public function assertPostalCodeSource($value) {
		df_assert($value, 'Администратор магазина должен указать почтовый индекс склада магазина.');
		df_param_string($value, 0);
		/** @var Zend_Validate_PostCode $validator */
		$validator = new Zend_Validate_PostCode(array('locale' => 'ru_RU'));
		if (!$validator->isValid($value)) {
			df_error(
				'Система не понимает указанный администратором почтовый индекс склада магазина: «%s».'
				,$value
			);
		}
		return $this;
	}

	/**
	 * @used-by Df_Shipping_Model_Method::throwExceptionInvalidWeight()
	 * @param float $weightInKilogrammes
	 * @return string
	 */
	public function formatWeight($weightInKilogrammes) {
		return sprintf(is_int($weightInKilogrammes) ? '%d' : '%.1f', $weightInKilogrammes);
	}

	/** @return Mage_Shipping_Model_Shipping */
	public function getMagentoMainShippingModel() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_model('shipping/shipping');
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $message
	 * @param Varien_Object|null $caller [optional]
	 * @return Df_Shipping_Helper_Data
	 */
	public function log($message, $caller = null) {
		/** @var string $moduleName */
		$moduleName = !$caller ? 'shipping' : Df_Core_Model_ClassManager::s()->getFeatureCode($caller);
		df()->debug()->report(rm_sprintf('rm.%s-{date}-{time}.log', $moduleName), $message);
		return $this;
	}

	const _CLASS = __CLASS__;
	/** @return Df_Shipping_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}