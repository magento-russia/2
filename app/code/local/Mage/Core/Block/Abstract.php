<?php
/**
 * Цели перекрытия системного класса:
 * 1) Ускорение @see Mage_Core_Block_Abstract::__()
 * 2) Поддержка @see Mage_Core_Block_Abstract::getCacheKeyInfo() для Magento CE 1.4.0.1
 * (этот метод появился только в Magento CE 1.4.1.0)
 * 3) Устранение необходимости заплатки http://magento-forum.ru/topic/1777/
 * для работы модуля «Иллюстрированное меню товарных разделов» в Magento CE 1.4.0.1
 * 4) @see toHtmlFast()
 */
class Mage_Core_Block_Abstract extends Mage_1930_Core_Block_Abstract {
	/**
	 * 2015-03-09
	 * Метод перекрыт ради ускорения его работы.
	 * @override
	 * @see Mage_1920_Core_Block_Abstract::__()
	 * @return string
	 */
	public function __() {$a = func_get_args(); return rm_translate($a, $this->getModuleName());}

	/**
	 * 2015-03-30
	 * Упрощённый и ускоренный вариант @see toHtml() для простых ситуаций.
	 * @used-by rm_render()
	 * @return string
	 */
	public function toHtmlFast() {
		/** @var string|false $result */
		$result = $this->_loadCache();
		if (false === $result) {
			$this->_beforeToHtml();
			$result = $this->_toHtml();
			$this->_saveCache($result);
		}
		return $this->_afterToHtml($result);
	}
}
