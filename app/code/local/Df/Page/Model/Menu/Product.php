<?php
abstract class Df_Page_Model_Menu_Product extends Df_Core_Model_Abstract {
	/**
	 * @param Varien_Data_Tree_Node $node
	 * @return Df_Page_Model_Menu_Product
	 */
	abstract public function addNode(Varien_Data_Tree_Node $node);
	const _CLASS = __CLASS__;
}