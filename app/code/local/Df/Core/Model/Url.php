<?php
class Df_Core_Model_Url extends Mage_Core_Model_Url {
	/**
	 * Цель перекрытия —
	 * кэширование адресов страниц для ускорения их формирования.
	 * @override
	 * @param string|null $routePath [optional]
	 * @param array(string => mixed)|null $routeParams [optional]
	 * @return string
	 */
	public function getUrl($routePath = null, $routeParams = null) {
		/**
		 * Обратите внимание,
		 * что ядро Magento обычно использует индивидуальные экземпляры класса @see Mage_Core_Model_Url
		 * при каждом процессе построения веб-адреса.
		 * По этой причине кэширование вынесли в класс-одиночку @see Df_Core_Model_Cache_Url
		 * (чтобы кэш загружался и сохранялся единократно для объекта-одиночки).
		 */
		return Df_Core_Model_Cache_Url::s()->getUrl($this, $routePath, $routeParams);
	}

	/**
	 * @param string|null $routePath [optional]
	 * @param array(string => mixed)|null $routeParams [optional]
	 * @return string
	 */
	public function getUrlParent($routePath = null, $routeParams = null) {
		return parent::getUrl($routePath, $routeParams);
	}


	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Core_Model_Url
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}