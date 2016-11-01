<?php
namespace Df\YandexMarket;
use Df\YandexMarket\Category\Document as Document;
use Df\YandexMarket\Category\Node as Node;
use Df\YandexMarket\Category\Tree as Tree;
class Categories extends \Df_Core_Model {
	/** @return string[] */
	public static function paths() {return df_cache_get_simple(__METHOD__, function() {
		/** @var Tree $tree */
		$tree = new Tree;
		foreach (Document::rows() as $row) {
			self::processRow($tree, $row);
		}
		return $tree->paths();
	});}

	/**
	 * @static
	 * @param Tree $tree
	 * @param string[] $row
	 * @return void
	 */
	private static function processRow(Tree $tree, array $row) {
		/** @var Node|null $parent */
		$parent = null;
		/** @var bool $parentHasBeenJustCreated */
		$parentHasBeenJustCreated = false;
		foreach ($row as $nodeName) {
			/** @var string $nodeName */
			// Значения некоторых ячеек таблицы оканчиваются пробелом.
			// Например: «Красота »
			$nodeName = df_trim($nodeName);
			if (!$nodeName) {
				break;
			}
			/** @var string $nodeName */
			/** @var Node $node */
			$node = null;
			if (!$parentHasBeenJustCreated) {
				$node = $tree->findNodeByNameAndParent($nodeName, $parent);
			}
			if (is_null($node)) {
				$node = new Node(['id' => df_uid(), 'name' => $nodeName],'id', $tree, $parent);
				$tree->addNode($node, $parent);
				$parentHasBeenJustCreated = true;
			}
			$parent = $node;
		}
	;}
}