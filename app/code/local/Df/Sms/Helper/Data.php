<?php
class Df_Sms_Helper_Data extends Mage_Core_Helper_Abstract {
	/**
	 * @param string $receiver
	 * @param string $message
	 * @param Df_Core_Model_StoreM $store
	 * @return Df_Sms_Helper_Data
	 */
	public function send($receiver, $message, Df_Core_Model_StoreM $store) {
		if (df_cfgr()->sms()->general()->isEnabled($store)) {
			/** @var Df_Sms_Model_Gate $gate */
			$gate =
				df_model(
					df_cfgr()->sms()->general()->getGateClass($store)
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


	/** @return Df_Sms_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}