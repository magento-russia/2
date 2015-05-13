<?php
abstract class Df_Core_Model_RemoteControl_Action_Concrete extends Df_Core_Model_RemoteControl_Action {
	/** @return Df_Core_Model_RemoteControl_Message_Response */
	abstract protected function createMessageResponse();

	/** @return Df_Core_Model_RemoteControl_Message_Response */
	public function getMessageResponse() {
		if (!isset($this->{__METHOD__})) {
			try {
				$this->{__METHOD__} = $this->createMessageResponse();
			}
			catch(Exception $e) {
				$this->{__METHOD__} =
					Df_Core_Model_RemoteControl_Message_Response_GenericFailure::i(rm_ets($e))
				;
			}
			df_assert($this->{__METHOD__} instanceof Df_Core_Model_RemoteControl_Message_Response);
		};
		return $this->{__METHOD__};
	}

	/** @return Df_Core_Model_RemoteControl_Message_Request */
	protected function getMessageRequest() {return $this->cfg(self::P__MESSAGE_REQUEST);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__MESSAGE_REQUEST, Df_Core_Model_RemoteControl_Message_Request::_CLASS);
	}
	const _CLASS = __CLASS__;
	const P__MESSAGE_REQUEST = 'message_request';
}