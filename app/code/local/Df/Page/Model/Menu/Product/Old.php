<?php
class Df_Page_Model_Menu_Product_Old extends Df_Page_Model_Menu_Product {
	/**
	 * @override
	 * @param Varien_Data_Tree_Node $node
	 * @return Df_Page_Model_Menu_Product_Old
	 */
	public function addNode(Varien_Data_Tree_Node $node) {
		$this->getMenu()->addNode($node);
		return $this;
	}

	/** @return Varien_Data_Tree */
	private function getMenu() {return $this->cfg(self::P__MENU);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__MENU, 'Varien_Data_Tree');
	}
	const _CLASS = __CLASS__;
	const P__MENU = 'menu';
	/**
	 * @static
	 * @param Varien_Data_Tree $menu
	 * @return Df_Page_Model_Menu_Product_Old
	 */
	public static function i(Varien_Data_Tree $menu) {return new self(array(self::P__MENU => $menu));}
}