<?php
class Df_GoogleAnalytics_Block_Ga extends Mage_GoogleAnalytics_Block_Ga {
	/**
	 * @override
	 * @return string[]
	 */
	public function getCacheKeyInfo() {
		/** @var string[] $result */
		$result = parent::getCacheKeyInfo();
		if (
				df_module_enabled(Df_Core_Module::SPEED)
			&&
				df_cfg()->speed()->blockCaching()->googleAnalytics()
		) {
			$result =
				array_merge(
					$result
					,array(
						get_class($this)
						,Mage::app()->getStore()->getId()
						/**
						 * Здесь md5 не нужно,
						 * потому что @see Mage_Core_Block_Abstract::getCacheKey()
						 * использует аналогичную md5 функцию sha1
						 */
						,$this->getPageName()
					)
					,df_a($this->getData(), 'order_ids', array())
				)
			;
		}
		return $result;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		if (
				df_module_enabled(Df_Core_Module::SPEED)
			&&
				df_cfg()->speed()->blockCaching()->googleAnalytics()
		) {
			/**
			 * Чтобы блок кэшировался стандартным, заложенным в @see Mage_Core_Block_Abstract способом,
			 * продолжительность хранения кэша надо указывать обязательно,
			 * потому что значением продолжительности по умолчанию является «null»,
			 * что в контексте @see Mage_Core_Block_Abstract
			 * (и в полную противоположность Zend Framework
			 * и всем остальным частям Magento, где используется кэширование)
			 * означает, что блок не удет кэшироваться вовсе!
			 * @see Mage_Core_Block_Abstract::_loadCache()
			 */
			$this->setData('cache_lifetime', Df_Core_Block_Template::CACHE_LIFETIME_STANDARD);
		}
	}
}