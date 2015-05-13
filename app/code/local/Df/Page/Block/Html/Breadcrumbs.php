<?php
class Df_Page_Block_Html_Breadcrumbs extends Mage_Page_Block_Html_Breadcrumbs {
	/**
	 * Цель перекрытия —
	 * предоставление администратору возможности
	 * скрывать названия товара из навигационного меню витринной товарной карточки:
	 * @link http://magento-forum.ru/topic/4509/
	 *
	 * @override
	 * @param string $crumbName
	 * @param array(string => string) $crumbInfo
	 * @param bool $after [optional]
	 * @return Df_Page_Block_Html_Breadcrumbs
	 */
	public function addCrumb($crumbName, $crumbInfo, $after = false) {
		/** @var bool $needHideProductNameFromBreadcrumbs */
		static $needHideProductNameFromBreadcrumbs;
		if (!isset($needHideProductNameFromBreadcrumbs)) {
			$needHideProductNameFromBreadcrumbs =
				Df_Tweaks_Model_Settings_Catalog_Product_View::s()->needHideProductNameFromBreadcrumbs()
			;
		}
		if (!($needHideProductNameFromBreadcrumbs && ('product' === $crumbName))) {
			$crumbInfo['label'] = $this->__(df_a($crumbInfo, 'label', ''));
			$crumbInfo['title'] = $this->__(df_a($crumbInfo, 'title', ''));
			parent::addCrumb($crumbName, $crumbInfo, $after);
		}
		return $this;
	}

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
				df_cfg()->speed()->blockCaching()->pageHtmlBreadcrumbs()
		) {
			$result =
				array_merge(
					$result
					,array(get_class($this))
					,$this->calculateCrumbCacheKeys()
				)
			;
		}
		return $result;
	}

	/** @return string[] */
	private function calculateCrumbCacheKeys() {
		return is_array($this->_crumbs) ? array_keys($this->_crumbs) : array();
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
				df_cfg()->speed()->blockCaching()->pageHtmlBreadcrumbs()
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