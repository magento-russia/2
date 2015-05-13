<?php
class Df_Cms_Model_Registry extends Df_Core_Model_DestructableSingleton {
	/** @return Df_Cms_Model_ContentsMenu_Applicator[] */
	public function getApplicators() {
		if (!is_array($this->_applicators)) {
			$result = array();
			foreach ($this->getCmsRootNodes() as $cmsRootNode) {
				/** @var Df_Cms_Model_Hierarchy_Node $cmsRootNode */
				/**
				 * @see Df_Cms_Model_ContentsMenu_Applicator говорит,
				 * должна ли данная рубрика отображаться в каком-либо меню на текущей странице
				 */
				$result[]= Df_Cms_Model_ContentsMenu_Applicator::i($cmsRootNode);
			}
			$this->_applicators = $result;
		}
		return $this->_applicators;
	}
	/** @var Df_Cms_Model_ContentsMenu_Applicator[]|null */
	protected $_applicators;

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
	protected function getPropertiesToCachePerStore() {return array('_applicators');}

	/** @return Df_Cms_Model_Resource_Hierarchy_Node_Collection */
	private function getCmsRootNodes() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Cms_Model_Resource_Hierarchy_Node_Collection $result */
			$result = Df_Cms_Model_Hierarchy_Node::c();
			$result
				->addStoreFilter(Mage::app()->getStore(), false)
				->addRootNodeFilter()
				->joinMetaData()
				->joinCmsPage()
			;
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Cms_Model_Registry */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}


 