<?php
class Df_Page_Block_Template_Links extends Mage_Page_Block_Template_Links {
	/**
	 * Цель перекрытия —
	 * устранение сбоя «Serialization of 'Mage_Core_Model_Layout_Element' is not allowed»
	 * оформительской темы ThemeForest Infortis Fortis:
	 * http://magento-forum.ru/topic/4837/
	 * @override
	 * @param string $label
	 * @param string $url
	 * @param string $title
	 * @param boolean $prepare
	 * @param array $urlParams
	 * @param int $position
	 * @param string|array $liParams
	 * @param string|array $aParams
	 * @param string $beforeText
	 * @param string $afterText
	 * @return Df_Page_Block_Template_Links
	 */
	public function addLink(
		$label, $url='', $title='', $prepare=false, $urlParams=array()
		, $position=null, $liParams=null, $aParams=null, $beforeText='', $afterText=''
	) {
		/**
		 * В Magento CE 1.9 $label и $title метод для перевода почему-то не вызывается в  шаблоне.
		 * @see app/design/frontend/rwd/default/template/page/template/links.phtml
		 */
		$label = $this->__($label);
		$title = $this->__($title);
		if ($beforeText instanceof SimpleXMLElement) {
			$beforeText = df_leaf_s($beforeText);
		}
		if ($afterText instanceof SimpleXMLElement) {
			$afterText = df_leaf_s($afterText);
		}
		parent::addLink(
			$label, $url, $title, $prepare, $urlParams, $position
			, $liParams, $aParams, $beforeText, $afterText
		);
		return $this;
	}

	/**
	 * @param string $blockType
	 * @return Df_Page_Block_Template_Links
	 */
	public function removeLinkByBlockType($blockType) {
		/** @var array $keysToUnset */
		$keysToUnset = array();
		foreach ($this->getLinks() as $key => $link) {
			/** @var Varien_Object $link */
			if ($link instanceof Mage_Core_Block_Abstract) {
				/** @var Mage_Core_Block_Abstract $link */
				if ($blockType === $link->getData('type')) {
 					$keysToUnset[]= $key;
				}
			}
		}
		foreach ($keysToUnset as $keyToUnset) {
			unset($this->_links[$keyToUnset]);
		}
		return $this;
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
				df_cfgr()->speed()->blockCaching()->pageTemplateLinks()
		) {
			/**
			 * Ключ кэша не устанавливаем, потому что это делает родительский класс
			 * @see Mage_Page_Block_Template_Links::getCacheKeyInfo
			 */
			$this->setData('cache_lifetime', Df_Core_Block_Template::CACHE_LIFETIME_STANDARD);
		}
	}
}