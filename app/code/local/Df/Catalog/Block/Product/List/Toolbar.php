<?php
class Df_Catalog_Block_Product_List_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar {
	/**
	 * @override
	 * @see Mage_Core_Block_Template::getCacheKeyInfo()
	 * @used-by Df_Core_Block_Abstract::getCacheKey()
	 * @return string[]
	 */
	public function getCacheKeyInfo() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array_merge(parent::getCacheKeyInfo(), array(
				/**
				 * Обратите внимание, что товарный список может отображаться на главной странице,
				 * и тогда rm_state()->getCurrentCategoryId()
				 * вернёт идентификатор корневого товарного раздела.
				 */
				rm_state()->getCurrentCategoryId()
				/**
				 * К ключу кэширования надо также добавить
				 * значение текущего ключа пошаговой фильтрации.
				 * http://magento-forum.ru/topic/4472/
				 * Обратите внимание, что мы не знаем название ключа пошаговой фильтрации,
				 * потому что это название равно названию некоего товарного свойства,
				 * по которому осуществляется пошаговая фильтрация.
				 * http://localhost.com:719/bed1.html?p=2&vid_tovara=12
				 * Здесь название ключа: «vid_tovara».
				 * Более того, таких ключей в веб-адресе может быть даже несколько.
				 * Поэтому, я так понимаю, что лучшим решением будет
				 * добавлять к ключу кэширования сразу все параметры веб-адреса.
				 */
				, http_build_query($this->getRequest()->getParams())
				/**
				 * Раз мы добавили к ключу кэширования сразу все параметры веб-адреса,
				 * то следующие параметры уже добавлять к ключу кэширования не надо,
				 * ибо они вроде бы всегда передаются посредством параметров веб-адреса:
					,$this->getCurrentOrder()
					,$this->getCurrentPage()
					,$this->getCurrentDirection()
					,$this->getCurrentMode()
				 */
			));
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Core_Model_Cache */
	private function getCacheSettings() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Core_Model_Cache::i('config', false, Mage_Core_Model_Config::CACHE_TAG);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return int|bool|null
	 */
	public function getCacheLifetime() {return Df_Core_Block_Template::CACHE_LIFETIME_STANDARD;}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		/**
		 * Чтобы блок кэшировался стандартным, заложенным в @see Mage_Core_Block_Abstract способом,
		 * продолжительность хранения кэша надо указывать обязательно,
		 * потому что значением продолжительности по умолчанию является «null»,
		 * что в контексте @see Mage_Core_Block_Abstract
		 * (и в полную противоположность Zend Framework
		 * и всем остальным частям Magento, где используется кэширование)
		 * означает, что блок не удет кэшироваться вовсе!
		 * @used-by Mage_Core_Block_Abstract::_loadCache()
		 */
		$this->setData('cache_lifetime', Df_Core_Block_Template::CACHE_LIFETIME_STANDARD);
		/** @var string[] $cachedFields */
		$cachedFields = array('_availableMode', '_availableOrder', '_orderField');
		/** @var string $cacheKey */
		$cacheKey = $this->getCacheSettings()->makeKey(__METHOD__);
		/** @var array(string => mixed) $settings */
		$settings = $this->getCacheSettings()->loadDataArray($cacheKey);
		if ($settings) {
			foreach ($cachedFields as $cachedField) {
				/** @var string $cachedField */
				$this->{$cachedField} = $settings[$cachedField];
			}
			$this->setTemplate('catalog/product/list/toolbar.phtml');
		}
		else {
			parent::_construct();
			$settings = array();
			foreach ($cachedFields as $cachedField) {
				/** @var string $cachedField */
				$settings[$cachedField] = $this->{$cachedField};
			}
			$this->getCacheSettings()->saveDataArray($cacheKey, $settings);
		}
	}
}