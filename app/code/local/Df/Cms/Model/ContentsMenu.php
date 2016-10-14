<?php
class Df_Cms_Model_ContentsMenu extends Df_Core_Model {
	/** @return Df_Cms_Model_ContentsMenu_Applicator_Collection */
	public function getApplicators() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Cms_Model_ContentsMenu_Applicator_Collection::i();
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getPosition() {return $this->getApplicator()->getPosition();}

	/** @return int[] */
	public function getRootNodeIds() {
		if (!isset($this->{__METHOD__})) {
			/** @var int[] $result */
			$result = array();
			foreach ($this->getApplicators() as $applicator) {
				/** @var Df_Cms_Model_ContentsMenu_Applicator $applicator */
				/** @var Df_Cms_Model_Hierarchy_Node $node */
				$result[]= df_nat($applicator->getNode()->getId());
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	public function getVerticalOrdering() {return $this->getApplicator()->getVerticalOrdering();}

	/** @return Df_Cms_Model_ContentsMenu */
	public function insertIntoLayout() {
		if ($this->getBlockParent()) {
			$this->getBlockParent()->insert($this->getBlockMenu(), df_nts($this->getBlockSiblingName()));
		}
		return $this;
	}

	/**
	 * @param Df_Cms_Model_ContentsMenu $menu
	 * @return Df_Cms_Model_ContentsMenu
	 */
	public function merge(Df_Cms_Model_ContentsMenu $menu) {
		$this->getApplicators()->add($menu->getApplicators());
		return $this;
	}

	/** @return Df_Cms_Model_ContentsMenu_Applicator */
	private function getApplicator() {return $this->cfg(self::$P__APPLICATOR);}

	/** @return Df_Cms_Block_Frontend_Menu_Contents */
	private function getBlockMenu() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Cms_Block_Frontend_Menu_Contents::i($this);
		}
		return $this->{__METHOD__};
	}

	/** @return Mage_Core_Block_Abstract|null */
	private function getBlockParent() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(df_layout()->getBlock($this->getPosition()));
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return string|null */
	private function getBlockSiblingName() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|null $result */
			$result = null;
			if (!is_null($this->getBlockParent())) {
				/** @var int $childrenCount */
				$childrenCount = count($this->getBlockParent()->getSortedChildren());
				df_assert_integer($childrenCount);
				/** @var int $insertionIndex */
				$insertionIndex =
					max(
						0
						,min(
							$childrenCount - 1
							,/**
							 * Вычитает единицу,
							 * потому что в административном интерфейсе
							 * нумерация начинается с 1
							 */
							$this->getVerticalOrdering() - 1
						)
					)
				;
				df_assert_integer($insertionIndex);
				/** @var int $siblingIndex */
				$siblingIndex =
					min(
						$childrenCount - 1
						,/**
						 * Вычитает единицу,
						 * потому что в административном интерфейсе
						 * нумерация начинается с 1
						 */
						$insertionIndex + 1
					)
				;
				df_assert_integer($siblingIndex);
				/** @var string|null $result */
				$result = dfa($this->getBlockParent()->getSortedChildren() ,$siblingIndex);
			}
			$this->{__METHOD__} = rm_n_set($result);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__APPLICATOR, Df_Cms_Model_ContentsMenu_Applicator::_C);
	}
	/** @used-by Df_Cms_Model_ContentsMenu_Collection::itemClass() */
	const _C = __CLASS__;
	const POSITION__CONTENT = 'content';
	const POSITION__LEFT = 'left';
	const POSITION__RIGHT = 'right';
	/** @var string */
	private static $P__APPLICATOR = 'applicator';

	/**
	 * @static
	 * @param Df_Cms_Model_ContentsMenu_Applicator $applicator
	 * @return Df_Cms_Model_ContentsMenu
	 */
	public static function i(Df_Cms_Model_ContentsMenu_Applicator $applicator) {return new self(array(
		self::$P__APPLICATOR => $applicator
	));}
}