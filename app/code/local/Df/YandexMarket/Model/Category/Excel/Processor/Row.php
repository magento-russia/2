<?php
class Df_YandexMarket_Model_Category_Excel_Processor_Row extends Df_Core_Model_Abstract {
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
							'id' => rm_uniqid()
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
	private function getRow() {
		return $this->cfg(self::P__ROW);
	}

	/** @return Df_YandexMarket_Model_Category_Tree */
	private function getTree() {
		return $this->cfg(self::P__TREE);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__TREE, Df_YandexMarket_Model_Category_Tree::_CLASS)
			->_prop(self::P__ROW, self::V_ARRAY)
		;
	}
	const _CLASS = __CLASS__;
	const P__TREE = 'tree';
	const P__ROW = 'row';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_YandexMarket_Model_Category_Excel_Processor_Row
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}