<?php
class Df_Reward_TestController extends Mage_Core_Controller_Front_Action {
	/** @return void */
	public function indexAction() {
		try {
			Df_Reward_Model_Observer::i()->scheduledPointsExpiration();
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e, true);
		}
	}
}