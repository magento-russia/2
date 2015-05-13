<?php
class Df_Sms_Helper_Data extends Mage_Core_Helper_Abstract {
	/**
	 * @param string $receiver
	 * @param string $message
	 * @param Mage_Core_Model_Store $store
	 * @return Df_Sms_Helper_Data
	 */
	public function send($receiver, $message, Mage_Core_Model_Store $store) {
		if (
				df_cfg()->sms()->general()->isEnabled($store)
			&&
				df_enabled(Df_Core_Feature::SMS, $store)
		) {
			/** @var Df_Sms_Model_Gate $gate */
			$gate =
				df_model(
					df_cfg()->sms()->general()->getGateClass($store)
					,array(
						Df_Sms_Model_Gate::P__MESSAGE =>
							Df_Sms_Model_Message::i(
								array(
									Df_Sms_Model_Message::P__BODY => $message
									,Df_Sms_Model_Message::P__RECEIVER => $receiver
								)
							)
						,Df_Sms_Model_Gate::P__STORE => $store
					)
				)
			;
			df_assert($gate instanceof Df_Sms_Model_Gate);
			$gate->send();
		}
		return $this;
	}

	const _CLASS = __CLASS__;
	/** @return Df_Sms_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}