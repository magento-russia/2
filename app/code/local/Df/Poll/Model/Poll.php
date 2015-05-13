<?php
class Df_Poll_Model_Poll extends Mage_Poll_Model_Poll {
	/**
	 * @override
	 * @return Mage_Poll_Model_Poll_Answer[]|Df_Poll_Model_Resource_Poll_Answer_Collection
	 */
	public function getAnswers() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Poll_Model_Poll_Answer[]|Df_Poll_Model_Resource_Poll_Answer_Collection $result */
			$result = parent::getAnswers();
			if (!$result && $this->getId()) {
				$result = Df_Poll_Model_Resource_Poll_Answer_Collection::i();
				$result->addPollFilter($this->getId());
				Df_Varien_Data_Collection::unsetDataChanges($result);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Poll_Model_Resource_Poll::mf());
	}
	const _CLASS = __CLASS__;
	/** @return string */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
	/** @return Df_Poll_Model_Poll */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}