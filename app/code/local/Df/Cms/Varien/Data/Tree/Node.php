<?php
class Df_Cms_Varien_Data_Tree_Node extends Varien_Data_Tree_Node {
	/** @return Df_Cms_Model_Hierarchy_Node */
	public function getCmsNode() {return $this->_getData(self::P__CMS_NODE);}
	const P__CMS_NODE = 'cms_node';
}