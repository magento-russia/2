<?php
class Df_Core_Model_Resource_Store extends Mage_Core_Model_Resource_Store {
	/**
	 * 2015-08-03
	 * Обратите внимание,
	 * что это перекрытие нужно не только для устранения дефекта CE версий ранее 1.7.0.0,
	 * но и потому что у нас метод @see Df_Core_Model_StoreM::getId()
	 * возвращает целое число, а не строку,
	 * и стандартная проверка ядра $object->getId() === '0' уже не является корректной.
	 * @override
	 * @param Mage_Core_Model_Abstract|Df_Core_Model_Store $object
	 * @return Df_Core_Model_Resource_Store
	 * @throws Mage_Core_Exception
	 */
	protected function _checkUnique(Mage_Core_Model_Abstract $object) {
		Df_Core_Model_Resource_Db_UniqueChecker::check(
			$object, $this->_getWriteAdapter(), $this->_prepareDataForSave($object)
		);
		return $this;
	}

	/**
	 * 2015-02-09
	 * Возвращаем объект-одиночку именно таким способом,
	 * потому что наш класс перекрывает посредством <rewrite> системный класс,
	 * и мы хотим, чтобы вызов @see Mage::getResourceSingleton() ядром Magento
	 * возвращал тот же объект, что и наш метод @see s(),
	 * сохраняя тем самым объект одиночкой (это важно, например, для производительности:
	 * сохраняя объект одиночкой — мы сохраняем его кэш между всеми пользователями объекта).
	 * @return Df_Core_Model_Resource_Store
	 */
	public static function s() {return Mage::getResourceSingleton('core/store');}
}