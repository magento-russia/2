<?php
/**
 * @method Df_Parser_Model_Category_Node getParent()
 */
class Df_Parser_Model_Category_Node extends Df_Varien_Data_Tree_Node {
	/** @return Df_Parser_Model_Category */
	public function getCategory() {return $this->cfg(self::P__CATEGORY);}

	/**
	 * @override
	 * @return string
	 */
	public function getId() {return strval($this->getCategory()->getId());}

	/**
	 * @override
	 * @return string
	 */
	public function getName() {return $this->getCategory()->getName();}

	/** @return Zend_Uri_Http */
	public function getUri() {return $this->getCategory()->getUri();}

	const _C = __CLASS__;
	const P__CATEGORY = 'category';
}