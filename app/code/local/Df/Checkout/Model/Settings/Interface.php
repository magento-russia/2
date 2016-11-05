<?php
class Df_Checkout_Model_Settings_Interface extends Df_Core_Model_Settings {
	/** @return boolean */
	public function needShowAllStepsAtOnce() {
		/** @var bool $result */
		static $result;
		if (is_null($result)) {
			$result = $this->getYesNo('df_checkout/interface/show_all_steps_at_once');
		}
		return $result;
	}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}