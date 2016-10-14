<?php
class Df_Cms_Model_Registry extends Df_Core_Model {
	/** @return Df_Cms_Model_ContentsMenu_Applicator[] */
	public function getApplicators() {
		if (!is_array($this->_applicators)) {
			/** @uses Df_Cms_Model_ContentsMenu_Applicator::i() */
			$this->_applicators = $this->getCmsRootNodes()->walk('Df_Cms_Model_ContentsMenu_Applicator::i');
		}
		return $this->_applicators;
	}
	/**
	 * @used-by propertiesToCachePerStore()
	 * @var Df_Cms_Model_ContentsMenu_Applicator[]|null
	 */
	protected $_applicators;

	/**
	 * @used-by Df_Core_Model::cacheSaveProperty()
	 * @override
	 * @return string[]
	 */
	protected function cacheTags() {return array(Df_Cms_Model_Cache::TAG);}

	/**
	 * @override
	 * @return string
	 */
	protected function cacheType() {return Df_Cms_Model_Cache::TYPE;}

	/**
	 * @override
	 * @see Df_Core_Model::cachedObjects()
	 * @return string[]
	 */
	protected function cachedObjects() {return array('_applicators');}

	/** @return Df_Cms_Model_Resource_Hierarchy_Node_Collection */
	private function getCmsRootNodes() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Cms_Model_Resource_Hierarchy_Node_Collection $result */
			$result = Df_Cms_Model_Hierarchy_Node::c();
			$result
				->addStoreFilter(rm_store(), false)
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


 