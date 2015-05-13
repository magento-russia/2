<?php
class Df_Admin_Model_Action_DeleteDemoStore extends Df_Core_Model_Controller_Action_Admin {
	/**
	 * @param Mage_Core_Model_Store $store
	 * @return string
	 */
	public function getLink(Mage_Core_Model_Store $store) {
		return rm_url_admin(
			'df_admin/notification/deleteDemoStore', array(self::$RP__STORE => $store->getCode())
		);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function generateResponseBody() {
		try {
			Df_Core_Model_Store::deleteStatic($this->getStore());
		}
		catch (Exception $e) {
			rm_exception_to_session($e);
		}
		$this->getController()->redirectReferer();
		return '';
	}

	/** @return Mage_Core_Model_Store */
	private function getStore() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Mage::app()->getStore($this->getStoreCode());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getStoreCode() {return df_request(self::$RP__STORE);}

	/** @var string */
	private static $RP__STORE = 'store';
	/**
	 * @static
	 * @param Df_Admin_NotificationController $controller
	 * @return Df_Admin_Model_Action_DeleteDemoStore
	 */
	public static function i(Df_Admin_NotificationController $controller) {
		return new self(array(self::P__CONTROLLER => $controller));
	}
	/** @return Df_Admin_Model_Action_DeleteDemoStore */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}