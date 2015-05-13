<?php
class Df_Payment_Helper_DataM extends Mage_Payment_Helper_Data {
	/**
	 * Цель перекрытия:
	 *
	 * Этот метод работает точь-в-точь как родительский
	 * и перекрыт лишь для замены конструкций вида $a->sort_order на $a->getData('sort_order').
	 * Свойство sort_order отсутствует у класса @see Mage_Payment_Model_Method_Abstract,
	 * однако в Magento Community Edition использование этого свойства работает
	 * по причине наличия метода @see Varien_Object::__get(),
	 * В Российской сборке Magento метод @see Varien_Object::__get() мешал и я его удалил
	 * путём перекрытия класса Varien_Object в области local,
	 * поэтому нам надо и избавиться от конструкций вида $a->sort_order
	 * @overide
	 * @param mixed|Varien_Object $a
	 * @param mixed|Varien_Object $b
	 * @return int
	 */
	protected function _sortMethods($a, $b) {
		/** @var int $result */
		$result = 0;
		if (
				is_object($a) && ($a instanceof Varien_Object)
			&&
				is_object($b) && ($b instanceof Varien_Object)
		) {
			/** @var int $orderingA */
			$orderingA = intval($a->getData('sort_order'));
			/** @var int $orderingB */
			$orderingB = intval($b->getData('sort_order'));
			$result = $orderingA < $orderingB ? -1 : ($orderingA > $orderingB ? 1 : 0);
		}
		return $result;
	}
}


 