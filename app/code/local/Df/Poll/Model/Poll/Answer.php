<?php
class Df_Poll_Model_Poll_Answer extends Mage_Poll_Model_Poll_Answer {
	/**
	 * @override
	 * @param int $pollId
	 * @return Df_Poll_Model_Poll_Answer
	 */
	public function setPollId($pollId) {
		/**
		 * Избегаем пометки объекта как изменённого
		 * в том случае, когда прежни идентификатор опроса совпадает с новым.
		 * @see Mage_Poll_Model_Resource_Poll::_afterSave():
			foreach ($object->getAnswers() as $answer) {
				$answer->setPollId($object->getId());
				$answer->save();
			}
		 */
		if (intval($pollId) !== intval($this->getPollId())) {
			parent::setPollId($pollId);
		}
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Poll_Model_Resource_Poll_Answer::mf());
	}
	const _CLASS = __CLASS__;
	/** @return string */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
	/** @return Df_Poll_Model_Poll_Answer */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}