<?php
class Df_YandexMarket_Model_Category_Excel_Processor_Row extends Df_Core_Model {
	/** @return Df_YandexMarket_Model_Category_Excel_Processor_Row */
	public function process() {
		/** @var Df_YandexMarket_Model_Category_Node|null $parent */
		$parent = null;
		/** @var bool $parentHasBeenJustCreated */
		$parentHasBeenJustCreated = false;
		foreach ($this->getRow() as $nodeName) {
			// Значения некоторых ячеек таблицы оканчиваются пробелом.
			// Например: «Красота »
			$nodeName = df_trim($nodeName);
			if (!$nodeName) {
				break;
			}
			/** @var string $nodeName */
			/** @var Df_YandexMarket_Model_Category_Node $node */
			$node = null;
			if (!$parentHasBeenJustCreated) {
				$node = $this->getTree()->findNodeByNameAndParent($nodeName, $parent);
			}
			if (is_null($node)) {
				$node =
					new Df_YandexMarket_Model_Category_Node(
						$data = array(
							'id' => df_uid()
							,'name' => $nodeName
						)
						,$idField = 'id'
						,$tree = $this->getTree()
						,$parent = $parent
					)
				;
				$this->getTree()->addNode($node, $parent);
				$parentHasBeenJustCreated = true;
			}
			$parent = $node;
		}
		return $this;
	}

	/** @return string[] */
	private function getRow() {return $this->cfg(self::$P__ROW);}

	/** @return Df_YandexMarket_Model_Category_Tree */
	private function getTree() {return $this->cfg(self::$P__TREE);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__TREE, Df_YandexMarket_Model_Category_Tree::_C)
			->_prop(self::$P__ROW, RM_V_ARRAY)
		;
	}
	const _C = __CLASS__;
	/** @var string */
	private static $P__ROW = 'row';
	/** @var string */
	private static $P__TREE = 'tree';
	/**
	 * @static
	 * @param Df_YandexMarket_Model_Category_Tree $tree
	 * @param string[] $row
	 * @return Df_YandexMarket_Model_Category_Excel_Processor_Row
	 */
	public static function i(Df_YandexMarket_Model_Category_Tree $tree, array $row) {
		return new self(array(self::$P__TREE => $tree, self::$P__ROW => $row));
	}
}