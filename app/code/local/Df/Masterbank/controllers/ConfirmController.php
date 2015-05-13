<?php
class Df_Masterbank_ConfirmController extends Mage_Core_Controller_Front_Action {
	/** @return void */
	public function indexAction() {
		$this->getAction()->process();
	}
	
	/** @return Df_Masterbank_Model_Action_Confirm */
	private function getAction() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				!$this->getActionClass()
				? df_error('Непонятный запрос')
				: df_model(
					$this->getActionClass()
					, array(Df_Masterbank_Model_Action_Confirm::P__CONTROLLER => $this)
				)
			;
			df_assert($this->{__METHOD__} instanceof Df_Masterbank_Model_Action_Confirm);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getActionClass() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_a(
					array(
						0 => Df_Masterbank_Model_Action_Confirm_Payment::_CLASS
						, 21 =>  Df_Masterbank_Model_Action_Confirm_Capture::_CLASS
						, 24 => Df_Masterbank_Model_Action_Confirm_Void::_CLASS
					)
					,rm_nat0($this->getRequest()->getParam('TRTYPE'))
					,''
				)			
			;
		}
		return $this->{__METHOD__};
	}
}