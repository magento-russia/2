<?php
use Df_Varien_Data_Tree_Node as Node;
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
			/** @noinspection PhpUndefinedMethodInspection */
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

	/**
	 * @used-by \Df\YandexMarket\Categories::paths()
	 * @return string[]
	 */
	public function paths() {return dfc($this, function() {return
		df_clean(df_map(function(Node $n) {return
			$n->hasChildren() ? null : $n->getPathAsText()
		;}, $this->getTree()->getNodes()))
	;});}
}