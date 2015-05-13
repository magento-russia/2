<?php
abstract class Df_Core_Model_Event_CoreBlockAbstract_ToHtml_Abstract extends Df_Core_Model_Event {
	/** @return string */
	public function getBlockName() {
		/** @var string $result */
		$result =
			$this->getBlock()->getNameInLayout()
		;
		if (is_null($result)) {
			/**
			 * Оказывается, в макете могут присутствовать блоки с неуказанным типом.
			 * Например, Mage_GiftMessage_Block_Message_Inline.
			 */
			$result = '';
		}
		df_result_string($result);
		return $result;
	}

	/** @return string */
	public function getBlockType() {
		/** @var string $result */
		$result = $this->getBlock()->getData(Df_Core_Block_Template::P__TYPE);
		if (is_null($result)) {
			/**
			 * Оказывается, в макете могут присутствовать блоки с неуказанным типом.
			 * Например, @see Mage_GiftMessage_Block_Message_Inline.
			 */
			try {
				$result = rm_class_mf(get_class($this->getBlock()));
			}
			catch(Exception $e) {
			}
			if (is_null($result)) {
				$result = '';
			}
		}
		return $result;
	}

	/** @return Mage_Core_Block_Abstract */
	public function getBlock() {return $this->getEventParam(self::EVENT_PARAM__BLOCK);}

	const _CLASS = __CLASS__;
	const EVENT_PARAM__BLOCK = 'block';
}