<?php
abstract class Df_Varien_Data_Collection_Singleton extends Df_Varien_Data_Collection {
	/** @return void */
	abstract protected function loadInternal();

	/**
	 * @override
	 * @return int
	 */
	public function count() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = parent::count();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @param bool $printQuery [optional]
	 * @param bool $logQuery [optional]
	 * @return Df_Varien_Data_Collection
	 */
	public function load($printQuery = false, $logQuery = false) {
		/**
		 * Обратите внимание, что родительский класс @see Varien_Data_Collection
		 * никак не использует метод @see Varien_Data_Collection::isLoaded().
		 *
		 * Более того, ядро Magento НИКОГДА НЕ ВЫЗЫВАЕТ
		 * метод @see Varien_Data_Collection::isLoaded() вне класса
		 * (вызывает только внутри класса, $this->isLoaded()).
		 *
		 * Таким образом, в собственных классах мы вообще можем игнорировать методы
		 * @see Varien_Data_Collection::isLoaded(),
		 * @see Varien_Data_Collection::_setIsLoaded()
		 * и свойство @see Varien_Data_Collection::$_isCollectionLoaded
		 *
		 * С другой стороны, родительский класс
		 * вызывает свой метод @see Varien_Data_Collection::load()
		 * многократно в различных ситуациях (когда требуется доступ к данным):
		 * @see Varien_Data_Collection::getSize()
		 * @see Varien_Data_Collection::getFirstItem()
		 * @see Varien_Data_Collection::getLastItem()
		 * @see Varien_Data_Collection::getItems()
		 * @see Varien_Data_Collection::getColumnValues()
		 * @see Varien_Data_Collection::getItemsByColumnValue()
		 * @see Varien_Data_Collection::getItemByColumnValue()
		 * @see Varien_Data_Collection::getItemById()
		 * @see Varien_Data_Collection::getIterator()
		 * @see Varien_Data_Collection::count()
		 *
		 * При этом родительский класс НИКАК НЕ УЧИТЫВАЕТ, что этот метод уже вызывался раньше!
		 * Поэтому метод методы
		 * @see Varien_Data_Collection::isLoaded()
		 * @see Varien_Data_Collection::_setIsLoaded()
		 * всё-таки удобно и уместно использовать!
		 */
		if (!$this->isLoaded()) {
			$this->loadInternal();
			$this->_setIsLoaded(true);
		}
		return $this;
	}
}