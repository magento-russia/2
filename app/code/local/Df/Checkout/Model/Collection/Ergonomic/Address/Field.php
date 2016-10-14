<?php
class Df_Checkout_Model_Collection_Ergonomic_Address_Field extends Df_Varien_Data_Collection {
	/**
	 * @uses compareByWeight()
	 * @return void
	 */
	public function orderByWeight() {$this->uasort('compareByWeight');}

	/** @return void */
	public function removeHidden() {
		$this->removeItemsByKeys(
			/** @uses Df_Checkout_Block_Frontend_Ergonomic_Address_Field::needToShow() */
			array_diff(array_keys($this->_items), array_keys(array_filter($this->walk('needToShow'))))
		);
	}

	/**
	 * @override
	 * @see Df_Varien_Data_Collection::itemClass()
	 * @used-by Df_Varien_Data_Collection::addItem()
	 * @return string
	 */
	protected function itemClass() {return 'Df_Checkout_Block_Frontend_Ergonomic_Address_Field';}

	/** @return Df_Checkout_Model_Collection_Ergonomic_Address_Field */
	public static function i() {return new self;}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @used-by orderByWeight()
	 * @used-by uasort()
	 * http://php.net/manual/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * http://3v4l.org/khmlf
	 * @param Df_Checkout_Block_Frontend_Ergonomic_Address_Field $field1
	 * @param Df_Checkout_Block_Frontend_Ergonomic_Address_Field $field2
	 * @return int
	 */
	private static function compareByWeight(
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
}