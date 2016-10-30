<?php
class Df_Page_Block_Html_Breadcrumbs extends Mage_Page_Block_Html_Breadcrumbs {
	/**
	 * Цель перекрытия —
	 * предоставление администратору возможности
	 * скрывать названия товара из навигационного меню витринной товарной карточки:
	 * http://magento-forum.ru/topic/4509/
	 *
	 * @override
	 * @param string $crumbName
	 * @param array(string => string) $crumbInfo
	 * @param bool $after [optional]
	 * @return Df_Page_Block_Html_Breadcrumbs
	 */
	public function addCrumb($crumbName, $crumbInfo, $after = false) {
		/** @var bool $hide */
		static $hide; if (is_null($hide)) {$hide =
			Df_Tweaks_Model_Settings_Catalog_Product_View::s()->needHideProductNameFromBreadcrumbs()
		;}
		if (!$hide || 'product' !== $crumbName) {
			$crumbInfo['label'] = $this->__(dfa($crumbInfo, 'label', ''));
			$crumbInfo['title'] = $this->__(dfa($crumbInfo, 'title', ''));
			parent::addCrumb($crumbName, $crumbInfo, $after);
		}
		return $this;
	}

	/**
	 * 2015-03-12
	 * Обратите внимание, что родительский метод @see Mage_Page_Block_Html_Breadcrumbs::getCacheKeyInfo()
	 * появился только в Magento CE 1.8.1.0.
	 * Мы проверяем его существование условием isset($result['crumbs'])
	 * Свои параметры ключа добавляем только при отсутствии родительской реализации.
	 * @override
	 * @see Mage_Page_Block_Html_Breadcrumbs::getCacheKeyInfo()
	 * @used-by Df_Core_Block_Abstract::getCacheKey()
	 * @return string[]
	 */
	public function getCacheKeyInfo() {
		/** @var string[] $result */
		$result = parent::getCacheKeyInfo();
		if (
				!isset($result['crumbs'])
			&&
				df_module_enabled(Df_Core_Module::SPEED)
			&&
				df_cfgr()->speed()->blockCaching()->pageHtmlBreadcrumbs()
		) {
			$result = array_merge($result, array(get_class($this)), array_keys(df_nta($this->_crumbs)));
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
				df_cfgr()->speed()->blockCaching()->pageHtmlBreadcrumbs()
		) {
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
		}
	}
}