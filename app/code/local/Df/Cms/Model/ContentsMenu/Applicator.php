<?php
class Df_Cms_Model_ContentsMenu_Applicator extends Df_Core_Model {
	/** @return Df_Cms_Model_Hierarchy_Node */
	public function getNode() {return $this->cfg(self::P__NODE);}

	/** @return string */
	public function getPosition() {return $this->getNodeMenuParam('position');}

	/** @return int */
	public function getVerticalOrdering() {return rm_int($this->getNodeMenuParam('vertical_ordering'));}

	/** @return bool */
	public function isApplicableToTheCurrentPage() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_cfg()->cms()->hierarchy()->isEnabled()
				&& rm_bool($this->getNodeMenuParam('enabled'))
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $paramName
	 * @return mixed
	 */
	private function getNodeMenuParam($paramName) {
		df_param_string($paramName, 0);
		if (!isset($this->{__METHOD__}[$paramName])) {
			/** @var mixed $result */
			$result =
				$this->getNode()->getData(
					Df_Cms_Model_Hierarchy_Node::getMetadataKeyForPageType(
						$this->getPageType()
						,$paramName
					)
				)
			;
			$this->{__METHOD__}[$paramName] = $result;
		}
		return $this->{__METHOD__}[$paramName];
	}

	/**
	 * Обратите внимание, что определение этого метода
	 * на уровне класса Df_Cms_Model_ContentsMenu_Applicator
	 * оправдывается тем, что для идентификации типов страниц CMS_FOREIGN / CMS_OWN
	 * требуется информация о конкретной рубрике.
	 * @return string
	 */
	private function getPageType() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = Df_Cms_Model_ContentsMenu_PageType::OTHER;
			foreach ($this->getPageTypeMap() as $type => $handle) {
				/** @var string $type */
				/** @var string $handle */
				df_assert_string($type);
				df_assert_string($handle);
				if (rm_handle_presents($handle)) {
					$result = $type;
					break;
				}
			}
			if (Df_Cms_Model_ContentsMenu_PageType::OTHER === $result) {
				if (
						rm_handle_presents(Df_Core_Model_Layout_Handle::CMS_PAGE)
					&&
						!is_null(df_h()->cms()->getCurrentNode())
				) {
					/**
					 * Самодельная страница.
					 * Надо определить: входит ли данная страница в текущее меню.
					 * Сделать это просто: у страницы есть свойство xpath, * которое хранит информацию о всех её предках.
					 */
					$result =
							df_h()->cms()->getCurrentNode()->isBelongTo(
								$this->getNode()->getId()
							)
						?
							Df_Cms_Model_ContentsMenu_PageType::CMS_OWN
						:
							Df_Cms_Model_ContentsMenu_PageType::CMS_FOREIGN
					;
				}
			}
			df_result_string($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return array */
	private function getPageTypeMap() {
		return array(
			Df_Cms_Model_ContentsMenu_PageType::ACCOUNT => 'customer_account'
			,Df_Cms_Model_ContentsMenu_PageType::CATALOG_PRODUCT_LIST => 'catalog_category_view'
			,Df_Cms_Model_ContentsMenu_PageType::CATALOG_PRODUCT_VIEW => 'catalog_product_view'
			,Df_Cms_Model_ContentsMenu_PageType::FRONT => 'cms_index_index'
		);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__NODE, Df_Cms_Model_Hierarchy_Node::_C);
	}
	/**
	 * @used-by Df_Cms_Model_ContentsMenu::_construct()
	 * @used-by Df_Cms_Model_ContentsMenu_Applicator_Collection::itemClass()
	 */
	const _C = __CLASS__;
	const P__NODE = 'node';
	/**
	 * @static
	 * @param Df_Cms_Model_Hierarchy_Node $node
	 * @return Df_Cms_Model_ContentsMenu_Applicator
	 */
	public static function i(Df_Cms_Model_Hierarchy_Node $node) {return new self(array(
		self::P__NODE => $node
	));}
}