<?php
abstract class Df_Core_Model_Filter_Collection
	extends Df_Core_Model_Abstract
	implements Zend_Filter_Interface {
	/** @return Zend_Validate_Interface */
	abstract protected function createValidator();

	/**
	 * @param mixed $value
	 * @throws Zend_Filter_Exception If filtering $value is impossible
	 * @return Df_Varien_Data_Collection
	 */
	public function filter($value) {
		/** @var array|Traversable $value */
		/** @var Df_Varien_Data_Collection $result */
		$result = $this->createResultCollection();
		$result->addValidator($this->createValidator());
		$result->addItems($value);
		return $result;
	}

	/**
	 * Создает коллецию - результат фильтрации.
	 * Потомки могут перекрытием этого метода создать коллекцию своего класса.
	 * Метод должен возвращать объект класса Df_Varien_Data_Collection или его потомков
	 * @return Df_Varien_Data_Collection
	 */
	protected function createResultCollection() {return new Df_Varien_Data_Collection();}

	const _CLASS = __CLASS__;
}