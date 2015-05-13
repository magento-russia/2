<?php
class Df_Newsletter_Model_Settings_Subscription extends Df_Core_Model_Settings {
	/** @return boolean */
	public function fixSubscriberStore() {
		return $this->getYesNo('df_newsletter/subscription/fix_subscriber_store');
	}
	/** @return Df_Newsletter_Model_Settings_Subscription */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}