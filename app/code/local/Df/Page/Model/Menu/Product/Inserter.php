<?php
class Df_Page_Model_Menu_Product_Inserter extends Df_Core_Model {
	/** @return Df_Page_Model_Menu_Product_Inserter */
	public function process() {
		foreach ($this->getMenuSources() as $menuSource) {
			/** @var Df_Page_Model_MenuSource $menuSource */
			foreach ($menuSource->getTree()->getNodes() as $node) {
				/** @var Df_Cms_Varien_Data_Tree_Node $node */
				if (is_null($node->getParent())) {
					$this->getMenu()->addNode($node);
				}
			}
		}
		return $this;
	}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getCacheTagsRm() {
		return array_merge(array(Mage_Core_Model_Config::CACHE_TAG), parent::getCacheTagsRm());
	}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getPropertiesToCache() {return self::m(__CLASS__, 'getMenuSourcesAsArray');}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getPropertiesToCacheSimple() {return $this->getPropertiesToCache();}

	/**
	 * @override
	 * @return bool
	 */
	protected function isCacheEnabled() {
		return parent::isCacheEnabled() && Mage::app()->useCache(Mage_Core_Model_Config::CACHE_TAG);
	}

	/**
	 * @param array(string => string|int)
	 * @return Df_Page_Model_MenuSource
	 */
	private function createMenuSource(array $menuSourceAsArray) {
		/** @var Df_Page_Model_MenuSource $result */
		$result = null;
		/** @var string $class */
		$class = df_a($menuSourceAsArray, 'class');
		df_assert_string_not_empty($class);
		/** @var int $weight */
		$weight = rm_nat0(df_a($menuSourceAsArray, 'weight', 0));
		/** @var Df_Page_Model_MenuSource $result */
		$result = df_model($class, array(Df_Page_Model_MenuSource::P__WEIGHT => $weight));
		df_assert($result instanceof Df_Page_Model_MenuSource);
		return $result;
	}
	
	/** @return Df_Page_Model_MenuSource[] */
	private function getMenuSources() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Page_Model_MenuSource[] $result  */
			$result = array();
			foreach ($this->getMenuSourcesAsArray() as $menuSourceAsArray) {
				/** @var array(string => string|int) $menuSourceAsArray */
				/** @var Df_Page_Model_MenuSource $menuSource */
				$menuSource = $this->createMenuSource($menuSourceAsArray);
				if ($menuSource->isEnabled()) {
					$result[]= $menuSource;
				}
			}
			usort($result, array('Df_Page_Model_MenuSource', 'sort'));
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}
	
	/** @return array(string => array(string => string|int)) */
	private function getMenuSourcesAsArray() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Core_Model_Config_Element|null $result */
			$node = df()->config()->getNodeByKey('df/menu/product');
			$this->{__METHOD__} = !$node ? array() : $node->asCanonicalArray();
		}
		return $this->{__METHOD__};
	}
	
	/** @return Df_Page_Model_Menu_Product */
	private function getMenu() {return $this->cfg(self::P__MENU);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__MENU, Df_Page_Model_Menu_Product::_CLASS);
	}
	const _CLASS = __CLASS__;
	const P__MENU = 'menu';
	/**
	 * @static
	 * @param Df_Page_Model_Menu_Product $menu
	 * @return Df_Page_Model_Menu_Product_Inserter
	 */
	public static function i(Df_Page_Model_Menu_Product $menu) {return new self(array(
		self::P__MENU => $menu
	));}
}