<?php
class Df_Checkout_Model_Filter_Ergonomic_Address_Field_Collection_Order_ByWeight
	extends Df_Core_Model_Abstract
	implements Zend_Filter_Interface {
	/**
	 * @override
	 * @param mixed $value
	 * @throws Zend_Filter_Exception If filtering $value is impossible
	 * @return Df_Checkout_Model_Collection_Ergonomic_Address_Field
	 */
	public function filter($value) {
		/** @var Df_Checkout_Model_Collection_Ergonomic_Address_Field $value */
		df_assert($value instanceof Df_Checkout_Model_Collection_Ergonomic_Address_Field);
		/** @var Df_Checkout_Model_Collection_Ergonomic_Address_Field $result */
		$result = Df_Checkout_Model_Collection_Ergonomic_Address_Field::i();
		/** @var array $sourceAsArray */
		$sourceAsArray = $value->toArrayOfObjects();
		df_assert_array($sourceAsArray);
		usort($sourceAsArray, array($this, 'sort'));
		df_assert_array($sourceAsArray);
		foreach ($sourceAsArray as $fieldConfig) {
			/** @var Df_Checkout_Block_Frontend_Ergonomic_Address_Field $fieldConfig */
			$result->addItem($fieldConfig);
		}
		return $result;
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @link http://php.net/manual/en/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * @link http://3v4l.org/khmlf
	 *
	 * @param Df_Checkout_Block_Frontend_Ergonomic_Address_Field $field1
	 * @param Df_Checkout_Block_Frontend_Ergonomic_Address_Field $field2
	 * @return int
	 */
	private function sort(
		Df_Checkout_Block_Frontend_Ergonomic_Address_Field $field1
		,Df_Checkout_Block_Frontend_Ergonomic_Address_Field $field2
	) {
		/** @var int $result */
		$result = $field1->getOrderingWeight() - $field2->getOrderingWeight();
		if (0 === $result) {
			$result = $field1->getOrderingInConfig() - $field2->getOrderingInConfig();
		}
		return $result;
	}

	const _CLASS = __CLASS__;
	/** @return Df_Checkout_Model_Filter_Ergonomic_Address_Field_Collection_Order_ByWeight */
	public static function i() {return new self;}
}