<?php
class Df_Cms_Model_Tree extends Df_Core_Model {
	/** @return Varien_Data_Tree */
	public function getTree() {
		if (!$this->_tree) {
			/** @var Varien_Data_Tree $result */
			$result = new Varien_Data_Tree();
			$this->_nodesMap = array();
			foreach ($this->getCmsNodes() as $cmsNode) {
				/** @var Df_Cms_Model_Hierarchy_Node $cmsNode */
				/** @var Df_Cms_Varien_Data_Tree_Node $varienNode */
				$varienNode = $this->convertCmsNodeToVarienDataTreeNode($cmsNode, $result);
				if (!$cmsNode->getParentNodeId()) {
					/**
					 * Корневой узел.
					 * Обратите внимание, что этот корневой узел перестанет быть корневым
					 * после добавления меню статей в общее товарное меню:
					 * @see Df_Page_Model_Menu_Product_Inserter::process():
						if (is_null($node->getParent())) {
							$this->getMenu()->addNode($node);
						}
					 *
					 * Раньше, до учёта этого факта,
					 * в системе происходил дефект пропадания меню статей из общего меню:
					 * @link http://magento-forum.ru/topic/4518/
					 *
					 * Меню статей пропадало из общего меню по следующей причине.
					 * После редактирования администратором товара
					 * система автоматически удаляет полностраничный кэш.
					 * В этом случае мы попадаем данный метод @see Df_Cms_Model_Tree::getTree().
					 * Но в данную точну программы мы уже не попадали,
					 * потому что система загружает из кэша дерево дерево @see $_tree.
					 *
					 * Однако, так как мы не учитывали, что корневой узел меню статей
					 * переставал быть корневым (у него появлялся родитель в виде общего товарного меню),
					 * в методе @see Df_Page_Model_Menu_Product_Inserter::process():
						if (is_null($node->getParent())) {
							$this->getMenu()->addNode($node);
						}
					 * условие if (is_null($node->getParent())) уже не срабатывало
					 * (ведь $node->getParent() корневого узла менб статей указывало на товарное меню),
					 * и, таким образом, меню статей не добавлялось в товарное меню.
					 *
					 * Для устранения этого дефекта помечаем корневой узел меню статей
					 * специальным флагом @see $RM__ROOT,
					 * а перед сохранением в кэше дерева @see $_tree
					 * удаляем у корневого узла мню статей родителя
					 * и возвращаем ему в качестве дерева наше дерево меню статей
					 * вместо дерева общего товарного меню.
					 */
					$varienNode->setData(self::$RM__ROOT, true);
					$result->addNode($varienNode);
					$this->_nodesMap[rm_nat($cmsNode->getId())] = $varienNode;
				}
				else {
					/**
					 * Некорневой узел.
					 * Надо найти родителя данного узла, и связать данный узел с родителем.
					 * Обратите внимание, что благодаря вызову
					 * Df_Cms_Model_Resource_Hierarchy_Node_Collection::setTreeOrder
					 * родительский узел уже должен присутствовать в дереве.
					 */
					/** @var Df_Cms_Varien_Data_Tree_Node|null $parentNode */
					$parentNode = $this->getParentForCmsNodeInVarienDataTree($cmsNode);
					if (!is_null($parentNode)) {
						$parentNode->addChild($varienNode);
						$this->_nodesMap[rm_nat($cmsNode->getId())] = $varienNode;
					}
				}
			}
			/**
			 * Дублируем детей в поле «children_nodes»,
			 * потому что это поле использует метод
			 * Mage_Catalog_Block_Navigation::_renderCategoryMenuItemHtml
			 * в случае, если Mage::helper('catalog/category_flat')->isEnabled()
			 */
			foreach ($result->getNodes() as $node) {
				/** @var Df_Cms_Varien_Data_Tree_Node $node */
				/** @var Varien_Data_Tree_Node_Collection $children */
				$children = $node->getChildren();
				$node->setData('children_nodes', $children->getNodes());
			}
			$this->_tree = $result;
		}
		return $this->_tree;
	}
	/** @var Df_Cms_Varien_Data_Tree_Node[]  */
	protected $_nodesMap = array();

	/** @var Varien_Data_Tree */
	protected $_tree;

	/**
	 * @override
	 * @return void
	 */
	protected function cacheSaveBefore() {
		/**
		 * Обратите внимание, что корневой узел перестанет быть корневым
		 * после добавления меню статей в общее товарное меню:
		 * @see Df_Page_Model_Menu_Product_Inserter::process():
			if (is_null($node->getParent())) {
				$this->getMenu()->addNode($node);
			}
		 *
		 * Раньше, до учёта этого факта,
		 * в системе происходил дефект пропадания меню статей из общего меню:
		 * @link http://magento-forum.ru/topic/4518/
		 *
		 * Меню статей пропадало из общего меню по следующей причине.
		 * После редактирования администратором товара
		 * система автоматически удаляет полностраничный кэш.
		 * В этом случае мы попадаем данный метод @see Df_Cms_Model_Tree::getTree().
		 * Но в данную точну программы мы уже не попадали,
		 * потому что система загружает из кэша дерево дерево @see $_tree.
		 *
		 * Однако, так как мы не учитывали, что корневой узел меню статей
		 * переставал быть корневым (у него появлялся родитель в виде общего товарного меню),
		 * в методе @see Df_Page_Model_Menu_Product_Inserter::process():
			if (is_null($node->getParent())) {
				$this->getMenu()->addNode($node);
			}
		 * условие if (is_null($node->getParent())) уже не срабатывало
		 * (ведь $node->getParent() корневого узла менб статей указывало на товарное меню),
		 * и, таким образом, меню статей не добавлялось в товарное меню.
		 *
		 * Для устранения этого дефекта помечаем корневой узел меню статей
		 * специальным флагом @see $RM__ROOT,
		 * а перед сохранением в кэше дерева @see $_tree
		 * удаляем у корневого узла мню статей родителя
		 * и возвращаем ему в качестве дерева наше дерево меню статей
		 * вместо дерева общего товарного меню.
		 */
		foreach ($this->getTree()->getNodes() as $node) {
			/** @var Df_Cms_Varien_Data_Tree_Node $node */
			if ($node->getData(self::$RM__ROOT)) {
				$node->setParent(null);
				$node->setTree($this->getTree());
			}
		}
		parent::cacheSaveBefore();
	}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getCacheTagsRm() {return array(Df_Cms_Model_Cache::TAG);}

	/**
	 * @override
	 * @return string
	 */
	protected function getCacheTypeRm() {return Df_Cms_Model_Cache::TYPE;}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getPropertiesToCachePerStore() {return array('_nodesMap', '_tree');}

	/**
	 * @param Df_Cms_Model_Hierarchy_Node $cmsNode
	 * @param Varien_Data_Tree $tree
	 * @return Df_Cms_Varien_Data_Tree_Node
	 */
	private function convertCmsNodeToVarienDataTreeNode(
		Df_Cms_Model_Hierarchy_Node $cmsNode, Varien_Data_Tree $tree
	) {
		/**
		 * TemplateMela использует старую схему работы с меню
		 * @link http://magento-forum.ru/forum/317/
		 */
		/** @var bool $isItTemplateMela */
		static $isItTemplateMela;
		if (!isset($isItTemplateMela)) {
				/** @var string[] $templateMelaMenuClasses */
				$templateMelaMenuClasses =
					array(
						'TM_CustomMenu_Block_Navigation'
						, 'Megnor_AdvancedMenu_Block_Navigation'
					)
				;
				$isItTemplateMela = false;
				/** @var Mage_Core_Block_Abstract $navigationBlock */
				$navigationBlock = rm_layout()->getBlockSingleton('catalog/navigation');
			    foreach ($templateMelaMenuClasses as $templateMelaMenuClass) {
					/** @var string $templateMelaMenuClass */
					$isItTemplateMela =
							@class_exists($templateMelaMenuClass)
						&&
							/**
							 * Обратите внимание,
							 * что блок может быть не только класса $templateMelaMenuClass,
							 * но и класса одного из потомков класса $templateMelaMenuClass,
							 * поэтому @see is_a() точнее, чем
							  		$templateMelaMenuClass
							  ===
							  		Mage::getConfig()->getBlockClassName('catalog/navigation')
							 */
							is_a($navigationBlock, $templateMelaMenuClass)
					;
					if ($isItTemplateMela) {
						break;
					}
				}
			;
		}
		/** @var Df_Cms_Varien_Data_Tree_Node $result */
		$result =
			new Df_Cms_Varien_Data_Tree_Node(
				$data = array(
					'name' => $cmsNode->getLabel()
					,/**
					 * Обратите внимание, что Magento 1.7 RC1 трактует флаг is_active иначе,
				 	 * чем предыдущие версии.
					 * В предыдущих версиях is_active означает, что рубрика подлежит публикации.
					 * В Magento 1.7 is_active означает, что рубрика является текущей
					 */
					'is_active' => df_magento_version('1.7', '<') || $isItTemplateMela
					,'id' => 'df-cms-' . $cmsNode->getId()
					,'url' =>
						is_null($cmsNode->getPageIdentifier())
						? 'javascript:void(0);'
						: $cmsNode->getUrl()
					,/**
					 * привязываем узел CMS к созданному на его основе узлу Varien_Data_Tree_Node
					 */
					'cms_node' => $cmsNode
				)
				,$idField = 'id'
				,$tree
				,$parent = null
			)
		;
		return $result;
	}

	/** @return Df_Cms_Model_Resource_Hierarchy_Node_Collection */
	private function getCmsNodes() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Cms_Model_Resource_Hierarchy_Node_Collection $result */
			$result = Df_Cms_Model_Hierarchy_Node::c();
			$result
				->addStoreFilter(Mage::app()->getStore(), false)
				->joinCmsPage()
				->joinMetaData()
				->filterExcludedPagesOut()
				->filterUnpublishedPagesOut()
				->setTreeOrder()
			;
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param Df_Cms_Model_Hierarchy_Node $cmsNode
	 * @return Df_Cms_Varien_Data_Tree_Node|null
	 */
	private function getParentForCmsNodeInVarienDataTree(Df_Cms_Model_Hierarchy_Node $cmsNode) {
		// Результат может быть равен null,
		// если родительская рубрика по каким-то причинам не должна отображаться в меню
		// (например, если так указано в настройках рубрики).
		return df_a($this->_nodesMap, $cmsNode->getParentNodeId());
	}

	const _CLASS = __CLASS__;
	/** @var string */
	private static $RM__ROOT = 'rm_root';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Cms_Model_Tree
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}