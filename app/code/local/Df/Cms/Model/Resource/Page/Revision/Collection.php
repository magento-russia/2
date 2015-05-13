<?php
class Df_Cms_Model_Resource_Page_Revision_Collection
	extends Df_Cms_Model_Resource_Page_Collection_Abstract {
	/**
	 * Joining version data to each revision.
	 * Columns which should be joined determined by parameter $cols.
	 *
	 * @param mixed $cols
	 * @return Df_Cms_Model_Resource_Page_Revision_Collection
	 */
	public function joinVersions($cols = '')
	{
		if (!$this->getFlag('versions_joined')) {
			$this->_map['fields']['version_id'] = 'ver_table.version_id';
			$this->_map['fields']['versionuser_user_id'] = 'ver_table.user_id';
			$columns = array(
				'version_id' => 'ver_table.version_id','access_level','version_user_id' => 'ver_table.user_id','label','version_number'
			);
			if (is_array($cols)) {
				$columns = array_merge($columns, $cols);
			} else if ($cols) {
				$columns[]= $cols;
			}
			$this->getSelect()->joinInner(
				array('ver_table' => rm_table('df_cms/page_version'))
				,'ver_table.version_id = main_table.version_id'
				, $columns
			);
			$this->setFlag('versions_joined');
		}
		return $this;
	}

	/**
	 * Add filtering by version id.
	 * Parameter $version can be int or object.
	 *
	 * @param int|Df_Cms_Model_Page_Version $version
	 * @return Df_Cms_Model_Resource_Page_Revision_Collection
	 */
	public function addVersionFilter($version)
	{
		if ($version instanceof Df_Cms_Model_Page_Version) {
			$version = $version->getId();
		}

		if (is_array($version)) {
			$version = array('in' => $version);
		}

		$this->addFieldToFilter('version_id', $version);
		return $this;
	}

	/**
	 * Add order by revision number in specified direction.
	 *
	 * @param string $dir
	 * @return Df_Cms_Model_Resource_Page_Revision_Collection
	 */
	public function addNumberSort($dir = 'desc')
	{
		$this->setOrder('revision_number', $dir);
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Cms_Model_Page_Revision::mf(), Df_Cms_Model_Resource_Page_Revision::mf());
	}
	const _CLASS = __CLASS__;

	/** @return Df_Cms_Model_Resource_Page_Revision_Collection */
	public static function i() {return new self;}
}