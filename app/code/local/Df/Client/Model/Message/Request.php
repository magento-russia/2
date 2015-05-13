<?php
abstract class Df_Client_Model_Message_Request extends Df_Core_Model_RemoteControl_Message_Request {
	/** @return Df_Client_Model_DelayedMessage */
	public function deleteDelayedMessage() {
		if ($this->getDelayedMessage()) {
			$this->getDelayedMessage()->delete();
			$this->unsetDelayedMessage();
		}
		return $this;
	}

	/** @return Df_Client_Model_DelayedMessage */
	public function saveDelayedMessage() {
		if (!$this->getDelayedMessage()) {
			/** @var Df_Client_Model_DelayedMessage $delayedMessage */
			$delayedMessage =
				Df_Client_Model_DelayedMessage::i(
					array(
						Df_Client_Model_DelayedMessage::P__BODY =>
							Df_Core_Model_RemoteControl_Coder::s()->encode(
								$this->getPersistentData()
							)
						,Df_Client_Model_DelayedMessage::P__CLASS_NAME =>
							Df_Core_Model_RemoteControl_Coder::s()->encodeClassName(
								$this->getCurrentClassNameInMagentoFormat()
							)
					)
				)
			;
			$delayedMessage->setDataChanges(true);
			$this->setDelayedMessage($delayedMessage);
		}
		$this->getDelayedMessage()
			->updateNumRetries()
			->save()
		;
		return $this;
	}

	/**
	 * @param Df_Client_Model_DelayedMessage $value
	 * @return Df_Client_Model_DelayedMessage
	 */
	public function setDelayedMessage(Df_Client_Model_DelayedMessage $value) {
		$this->_delayedMessage = $value;
		return $this;
	}

	/** @return Df_Client_Model_DelayedMessage */
	private function getDelayedMessage() {
		return $this->_delayedMessage;
	}

	/** @return Df_Client_Model_DelayedMessage */
	private function unsetDelayedMessage() {
		$this->_delayedMessage = null;
		return $this;
	}
	/** @var Df_Client_Model_DelayedMessage */
	private $_delayedMessage;

	const _CLASS = __CLASS__;
}