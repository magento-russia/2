<?php
/**
 * 2015-08-15
 * Обратите внимание, что объект данного класса всегда является одиночкой,
 * потому что создётся при обработке события, которое случается лишь единократно:
 * @used-by Df_Page_Observer::page_block_html_topmenu_gethtml_before()
 * @used-by Df_Page_Observer::rm_menu_top_add_submenu()
 */
class Df_Page_Model_Menu_Product_Inserter extends Df_Core_Model {
	/**
	 * @used-by Df_Core_Model::cacheSaveProperty()
	 * @override
	 * @return string[]
	 */
	protected function cacheTags() {
		return array_merge(array(Mage_Core_Model_Config::CACHE_TAG), parent::cacheTags());
	}

	/**
	 * @override
	 * @see Df_Core_Model::cacheType()
	 * @used-by Df_Core_Model::isCacheEnabled()
	 * @return bool
	 */
	protected function cacheType() {return Mage_Core_Model_Config::CACHE_TAG;}

	/**
	 * @param array(string => string|int)
	 * @return Df_Page_Model_MenuSource
	 */
	private function createMenuSource(array $menuSourceAsArray) {
		/** @var Df_Page_Model_MenuSource $result */
		$result = null;
		/** @var string $class */
		$class = dfa($menuSourceAsArray, 'class');
		df_assert_string_not_empty($class);
		/** @var int $weight */
		$weight = df_nat0(dfa($menuSourceAsArray, 'weight', 0));
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
			foreach (rm_config_a('df/menu/product') as $menuSourceAsArray) {
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
	
	/** @return Df_Page_Model_Menu_Product */
	private function getMenu() {return $this->cfg(self::$P__MENU);}

	/**
	 * @used-by p()
	 * @return void
	 */
	private function process() {
		foreach ($this->getMenuSources() as $menuSource) {
			/** @var Df_Page_Model_MenuSource $menuSource */
			foreach ($menuSource->getTree()->getNodes() as $node) {
				/** @var Df_Cms_Varien_Data_Tree_Node $node */
				if (is_null($node->getParent())) {
					$this->getMenu()->addNode($node);
				}
			}
		}
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__MENU, Df_Page_Model_Menu_Product::class);
	}
	/** @var string */
	private static $P__MENU = 'menu';
	/**
	 * 2015-08-15
	 * Обратите внимание, что объект данного класса всегда является одиночкой:
	 * @used-by Df_Page_Observer::page_block_html_topmenu_gethtml_before()
	 * @used-by Df_Page_Observer::rm_menu_top_add_submenu()
	 * @param Df_Page_Model_Menu_Product $menu
	 * @return void
	 */
	public static function p(Df_Page_Model_Menu_Product $menu) {
		/** @var Df_Page_Model_Menu_Product_Inserter $i */
		$i = new self(array(self::$P__MENU => $menu));
		$i->process();
	}
}