<?php
class Df_Core_Model_RemoteControl_Action_Front extends Df_Core_Model_RemoteControl_Action {
	/**
	 * @override
	 * @return Df_Core_Model_RemoteControl_Action
	 */
	public function process() {
		try {
			$this->getActionConcrete()->process();
			Df_Core_Model_RemoteControl_MessageSerializer_Http::serializeMessageResponse(
				$this->getResponse(), $this->getActionConcrete()->getMessageResponse()
			);
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
		return $this;
	}

	/** @return Df_Core_Model_RemoteControl_Action_Concrete */
	private function getActionConcrete() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_model($this->getMessageRequest()->getActionClass(), array(
					Df_Core_Model_RemoteControl_Action_Concrete::P__CONTROLLER =>
						$this->getController()
					,Df_Core_Model_RemoteControl_Action_Concrete::P__MESSAGE_REQUEST =>
						$this->getMessageRequest()
				))
			;
			df_assert($this->{__METHOD__} instanceof Df_Core_Model_RemoteControl_Action_Concrete);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Core_Model_RemoteControl_Message_Request */
	private function getMessageRequest() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Core_Model_RemoteControl_MessageSerializer_Http::restoreMessageRequest(
					$this->getController()->getRequest()
				)
			;
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
	/**
	 * Не указываем в сигнатуре класс параметра,
	 * потому что этот класс является контроллером, а для контроллеров не работает автозагрузка.
	 * @static
	 * @param Df_Client_IndexController $controller
	 * @return Df_Core_Model_RemoteControl_Action_Front
	 */
	public static function i($controller) {
		return new self(array(self::P__CONTROLLER => $controller));
	}
}