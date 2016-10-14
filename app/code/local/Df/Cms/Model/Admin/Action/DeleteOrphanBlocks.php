<?php
class Df_Cms_Model_Admin_Action_DeleteOrphanBlocks extends Df_Core_Model_Action_Admin {
	/** @return string */
	public function getLink() {return rm_url_admin('df_cms_admin/notification/deleteOrphanBlocks');}

	/**
	 * @override
	 * @see Df_Core_Model_Action::_process()
	 * @used-by Df_Core_Model_Action::process()
	 * @return void
	 */
	protected function _process() {
		Df_Cms_Model_Resource_Block::s()->findOrphanBlocks()->walk('delete');
		rm_cache_clean();
	}

	/**
	 * @static
	 * @param Df_Admin_NotificationController $c
	 * @return Df_Cms_Model_Admin_Action_DeleteOrphanBlocks
	 */
	public static function i(Df_Admin_NotificationController $c) {return self::ic(__CLASS__, $c);}

	/** @return Df_Cms_Model_Admin_Action_DeleteOrphanBlocks */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}