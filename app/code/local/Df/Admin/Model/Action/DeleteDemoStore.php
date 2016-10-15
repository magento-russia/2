<?php
class Df_Admin_Model_Action_DeleteDemoStore extends Df_Core_Model_Action_Admin {
	/**
	 * @override
	 * @see Df_Core_Model_Action::_process()
	 * @used-by Df_Core_Model_Action::process()
	 * @return void
	 */
	protected function _process() {Df_Core_Model_Store::deleteStatic($this->store());}

	/** @return string */
	private function getStoreCode() {return rm_request(self::$RP__STORE);}

	/**
	 * @used-by Df_Admin_Block_Notifier_DeleteDemoStore::getLink()
	 * @param Df_Core_Model_StoreM $store
	 * @return string
	 */
	public static function getLink(Df_Core_Model_StoreM $store) {
		return df_url_admin('df_admin/notification/deleteDemoStore', array(
			self::$RP__STORE => $store->getCode()
		));
	}

	/** @var string */
	private static $RP__STORE = 'store';
}