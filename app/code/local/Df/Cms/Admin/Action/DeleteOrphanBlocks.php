<?php
class Df_Cms_Admin_Action_DeleteOrphanBlocks extends Df_Core_Model_Action_Admin {
	/** @return string */
	public function getLink() {return df_url_backend('df_cms_admin/notification/deleteOrphanBlocks');}

	/**
	 * @override
	 * @see Df_Core_Model_Action::_process()
	 * @used-by Df_Core_Model_Action::process()
	 * @return void
	 */
	protected function _process() {
		Df_Cms_Model_Resource_Block::s()->findOrphanBlocks()->walk('delete');
		df_cache_clean();
	}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}