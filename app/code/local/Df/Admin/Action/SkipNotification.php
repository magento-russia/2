<?php
class Df_Admin_Action_SkipNotification extends Df_Core_Model_Action_Admin {
	/**
	 * @override
	 * @see Df_Core_Model_Action::_process()
	 * @used-by Df_Core_Model_Action::process()
	 * @return void
	 */
	protected function _process() {
		Mage::getConfig()->saveConfig(
			Df_Admin_Model_Notifier::getConfigPathSkipByClass(df_request(self::$RP__CLASS)), 1
		);
		Mage::getConfig()->reinit();
	}

	/**
	 * @used-by Df_Admin_Model_Notifier::getUrlSkip()
	 * @param string $class
	 * @return string
	 */
	public static function getLink($class) {
		return df_url_backend('df_admin/notification/skip', array(self::$RP__CLASS => $class));
	}

	/** @var string  */
	private static $RP__CLASS = 'class';
}