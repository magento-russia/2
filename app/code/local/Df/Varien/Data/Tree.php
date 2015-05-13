<?php
class Df_Varien_Data_Tree extends Varien_Data_Tree {
	/**
	 * @param string $name
	 * @param Varien_Data_Tree_Node|null $parent [optional]
	 * @return Varien_Data_Tree_Node|null
	 */
	public function findNodeByNameAndParent($name, $parent = null) {
		/** @var Varien_Data_Tree_Node $result */
		$result = null;
		foreach ($this->getNodes() as $node) {
			/** @var Varien_Data_Tree_Node $node */
			if (
					(
							(is_null($parent) && is_null($node->getParent()))
						||
							(
									!is_null($parent)
								&&
									!is_null($node->getParent())
								&&
									($parent->getId() === $node->getParent()->getId())
							)
					)
				&&
					($node->getName() === $name)
			) {
				$result = $node;
				break;
			}
		}
		return $result;
	}

	/** @return string[] */
	public function getNodesAsTextArray() {
		if (!isset($this->{__METHOD__})) {
			/** @var string[] $result */
			$result = array();
			foreach ($this->getTree()->getNodes() as $node) {
				/** @var Df_Varien_Data_Tree_Node $node */
				if (!$node->hasChildren()) {
					$result[]= $node->getPathAsText();
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
}