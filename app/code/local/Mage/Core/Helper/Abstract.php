<?php
abstract class Mage_Core_Helper_Abstract extends Mage_1930_Core_Helper_Abstract {
	/**
	 * 2015-03-09
	 * Метод перекрыт ради ускорения его работы
	 * @override
	 * @see Mage_1920_Core_Block_Abstract::__()
	 * @return string
	 */
	public function __() {$a = func_get_args(); return rm_translate($a, $this->_getModuleName());}
}