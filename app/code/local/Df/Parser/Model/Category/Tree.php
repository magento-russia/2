<?php
class Df_Parser_Model_Category_Tree extends Df_Varien_Data_Tree {
	/**
	 * @param Df_Parser_Model_Category $category
	 * @param Df_Parser_Model_Category_Node|null $parent [optional]
	 * @return Df_Parser_Model_Category_Node
	 */
	public function createNodeRm(Df_Parser_Model_Category $category, $parent = null) {
		/** @var Df_Parser_Model_Category_Node $result */
		$result =
			new Df_Parser_Model_Category_Node(
				$data = array(
					'id' => $category->getId()
					,Df_Parser_Model_Category_Node::P__CATEGORY => $category
				)
				,$idField = 'id'
				,$this
				,$parent
			)
		;
		$this->addNode($result, $parent);
		return $result;
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Parser_Model_Category_Tree
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/** @return Df_Parser_Model_Category_Tree */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}