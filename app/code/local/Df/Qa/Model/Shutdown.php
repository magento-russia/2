<?php
class Df_Qa_Model_Shutdown extends Df_Core_Model_Abstract {
	/** @return void */
	public function process() {
		/** @var Df_Qa_Model_Message_Failure_Error $error */
		$error =
			Df_Qa_Model_Message_Failure_Error::i(array(
				Df_Qa_Model_Message_Failure_Error::P__NEED_LOG_TO_FILE => true
				,Df_Qa_Model_Message_Failure_Error::P__NEED_NOTIFY_DEVELOPER => true
			))
		;
		if ($error->isFatal()) {
			$error->log();
		}
	}

	const _CLASS = __CLASS__;
	/** @return Df_Qa_Model_Shutdown */
	public static function i() {return new self;}
	/** @return void */
	public static function processStatic() {Df_Qa_Model_Shutdown::i()->process();}
}