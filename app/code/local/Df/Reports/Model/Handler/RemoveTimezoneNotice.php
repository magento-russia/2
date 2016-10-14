<?php
/**
 * @method Df_Core_Model_Event_Controller_Action_Layout_GenerateBlocksAfter getEvent()
 */
class Df_Reports_Model_Handler_RemoveTimezoneNotice extends Df_Core_Model_Handler {
	/**
	 * Метод-обработчик события
	 * @override
	 * @return void
	 */
	public function handle() {
		if ($this->getMessagesCollection()) {
			/** @var Mage_Core_Model_Message_Collection $newCollection */
			$newCollection = clone $this->getMessagesCollection();
			$this->getMessagesCollection()->clear();
			/** @var string $textToFind */
			$textToFind =
				df_mage()->adminhtmlHelper()->__(
					'This report depends on timezone configuration.'
					. ' Once timezone is changed, the lifetime statistics need to be refreshed.'
				)
			;
			foreach ($newCollection->getItems() as $message) {
				/** @var Mage_Core_Model_Message_Abstract $message */
				if (
						(Mage_Core_Model_Message::NOTICE === $message->getType())
					&&
						rm_contains($message->getCode(), $textToFind)
				) {
					continue;
				}
				$this->getMessagesCollection()->add($message);
			}
		}
	}

	/**
	 * Класс события (для валидации события)
	 * @override
	 * @return string
	 */
	protected function getEventClass() {
		return Df_Core_Model_Event_Controller_Action_Layout_GenerateBlocksAfter::_C;
	}

	/** @return Mage_Core_Model_Message_Collection|null */
	private function getMessagesCollection() {
		return !$this->getMessagesBlock() ? null : $this->getMessagesBlock()->getMessageCollection();
	}
	
	/**
	 * @return Mage_Core_Block_Messages|null
	 */
	private function getMessagesBlock() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(
				rm_empty_to_null($this->getEvent()->getLayout()->getBlock('messages'))
			);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @used-by Df_Reports_Observer::controller_action_layout_generate_blocks_after() */
	const _C = __CLASS__;
}