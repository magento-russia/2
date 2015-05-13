<?php
class Df_Cms_Model_Admin_Action_DeleteOrphanBlocks extends Df_Core_Model_Controller_Action_Admin {
	/** @return string */
	public function getLink() {return rm_url_admin('df_cms_admin/notification/deleteOrphanBlocks');}

	/**
	 * @override
	 * @return string
	 */
	protected function generateResponseBody() {
		try {
			Df_Cms_Model_Resource_Block::s()->findOrphanBlocks()->walk('delete');
			rm_cache_clean();
		}
		catch (Exception $e) {
			rm_exception_to_session($e);
		}
		$this->getController()->redirectReferer();
		return '';
	}
	/**
	 * @static
	 * @param Df_Cms_Admin_NotificationController $controller
	 * @return Df_Cms_Model_Admin_Action_DeleteOrphanBlocks
	 */
	public static function i(Df_Cms_Admin_NotificationController $controller) {
		return new self(array(self::P__CONTROLLER => $controller));
	}
	/** @return Df_Cms_Model_Admin_Action_DeleteOrphanBlocks */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}