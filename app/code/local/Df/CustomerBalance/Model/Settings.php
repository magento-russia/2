<?php
class Df_CustomerBalance_Model_Settings extends Df_Core_Model_Settings {
	/** @return string */
	public function getTransactionalEmailSender() {return $this->getStringNullable('email_identity');}
	/** @return int */
	public function getTransactionalEmailTemplateId() {return $this->getNatural('email_template');}
	/** @return boolean */
	public function isEnabled() {return $this->getYesNo('enabled');}
	/** @return boolean */
	public function needShowHistory() {return $this->getYesNo('show_history');}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_customer/balance/';}
	/** @return Df_CustomerBalance_Model_Settings */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}