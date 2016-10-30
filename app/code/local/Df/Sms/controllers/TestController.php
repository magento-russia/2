<?php
class Df_Sms_TestController extends Mage_Core_Controller_Front_Action {
	/** @return void */
	public function indexAction() {
		try {
			df_h()->sms()->send(
				$receiver = df_cfgr()->sms()->general()->getAdministratorPhone(df_store())
				,$message = 'давай дружить :-)'
				,df_store()
			);
			df_response_content_type($this->getResponse(), 'text/plain; charset=UTF-8');
			$this->getResponse()->setBody(__METHOD__);
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e, true);
		}
	}
}