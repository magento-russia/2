<?php
class Df_Poll_Model_Poll_Answer extends Mage_Poll_Model_Poll_Answer {
	/**
	 * @override
	 * @return Df_Poll_Model_Resource_Poll_Answer_Collection
	 */
	public function getResourceCollection() {return self::c();}

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
		if ((int)$pollId !== (int)$this->getPollId()) {
			parent::setPollId($pollId);
		}
		return $this;
	}

	/**
	 * @override
	 * @return Df_Poll_Model_Resource_Poll_Answer
	 * 2016-10-14
	 * В родительском классе метод переобъявлен через PHPDoc,
	 * и поэтому среда разработки думает, что он публичен.
	 */
	/** @noinspection PhpHierarchyChecksInspection */
	protected function _getResource() {return Df_Poll_Model_Resource_Poll_Answer::s();}

	/** @used-by Df_Poll_Model_Resource_Poll_Answer_Collection::_construct() */
	const _C = __CLASS__;
	/** @return Df_Poll_Model_Resource_Poll_Answer_Collection */
	public static function c() {return new Df_Poll_Model_Resource_Poll_Answer_Collection;}
	/** @return Df_Poll_Model_Poll_Answer */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}