<?php
class Df_Customer_Block_Account_Navigation extends Mage_Customer_Block_Account_Navigation {
	/**
	 * Данный метод не является перекрытием родительского.
	 * Он добавлен именно в этот класс,
	 * потому что он использует поле $_links родительского класса.
	 * @param string $path
	 * @return Df_Customer_Block_Account_Navigation
	 */
	public function removeLinkByPath($path) {
		$linkNamesToRemove = array();
		/** @var array $linkNamesToRemove */
		foreach ($this->_links as $name => $link) {
			/** @var Varien_Object $link */
			if ($path == $link->getData('path')) {
				$linkNamesToRemove[]= $name;
			}
		}
		foreach ($linkNamesToRemove as $name) {
			/** @var string $name */
			unset($this->_links[$name]);
		}
		return $this;
	}
}