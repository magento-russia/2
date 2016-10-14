<?php
abstract class Df_Page_Model_Menu_Product extends Df_Core_Model {
	/**
	 * @param Varien_Data_Tree_Node $node
	 * @return Df_Page_Model_Menu_Product
	 */
	abstract public function addNode(Varien_Data_Tree_Node $node);
	/** @used-by Df_Page_Model_Menu_Product_Inserter::_construct() */
	const _C = __CLASS__;
}