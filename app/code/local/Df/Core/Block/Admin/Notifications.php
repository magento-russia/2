<?php
class Df_Core_Block_Admin_Notifications extends Df_Core_Block_Admin {
	/**
	 * @override
	 * @return bool
	 */
	protected function needToShow() {return 0 < $this->getNotifiers()->count();}

	/** @return Df_Admin_Model_Notifier_Collection */
	protected function getNotifiers() {return Df_Admin_Model_Notifier_Collection::s();}
}