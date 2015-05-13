<?php
class Df_Assist_Block_Api_PaymentConfirmation_Error extends Df_Core_Block_Template {
	/** @return int */
	public function getFirstCode() {return 1;}
	/** @return int */
	public function getSecondCode() {return 0;}
	/**
	 * @override
	 * @return string
	 */
	public function getTemplate() {return 'df/assist/api/payment-confirmation/error.xml';}

	/** @return Df_Assist_Block_Api_PaymentConfirmation_Error */
	public static function i() {return df_block(__CLASS__);}
}