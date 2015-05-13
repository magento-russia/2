<?php
class Df_Core_Model_Message_Collection extends Mage_Core_Model_Message_Collection {
	/**
	 * В отличие от родительского метода, не добавляет сообщение в коллекцию,
	 * если сообщение данного типа и с данным текстом уже присутствует в коллекции.
	 * @override
	 * @param Mage_Core_Model_Message_Abstract $message
	 * @return Df_Core_Model_Message_Collection
	 */
	public function addMessage(Mage_Core_Model_Message_Abstract $message) {
		if (!isset($this->_messages[$message->getType()])) {
			$this->_messages[$message->getType()] = array();
		}
		/** @var bool $found */
		$found = false;
		foreach ($this->_messages[$message->getType()] as $existingMessage) {
			/** @var Mage_Core_Model_Message_Abstract $existingMessage */
			if ($message->getCode() === $existingMessage->getCode()) {
				$found = true;
				break;
			}
		}
		if (!$found) {
			$this->_messages[$message->getType()][]= $message;
			$this->_lastAddedMessage = $message;
		}
		return $this;
	}
}