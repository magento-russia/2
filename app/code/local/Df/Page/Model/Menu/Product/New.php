<?php
class Df_Page_Model_Menu_Product_New extends Df_Page_Model_Menu_Product {
	/**
	 * @override
	 * @param Varien_Data_Tree_Node $node
	 * @return Df_Page_Model_Menu_Product_New
	 */
	public function addNode(Varien_Data_Tree_Node $node) {
		$this->getMenu()->addChild($node);
		return $this;
	}

	/** @return Varien_Data_Tree_Node */
	private function getMenu() {return $this->cfg(self::P__MENU);}
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__MENU, 'Varien_Data_Tree_Node');
	}
	const _C = __CLASS__;
	const P__MENU = 'menu';
	/**
	 * @static
	 * @param Varien_Data_Tree_Node $menu
	 * @return Df_Page_Model_Menu_Product_New
	 */
	public static function i(Varien_Data_Tree_Node $menu) {return new self(array(
		self::P__MENU => $menu
	));}
}