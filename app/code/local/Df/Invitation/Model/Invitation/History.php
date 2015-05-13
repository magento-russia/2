<?php
class Df_Invitation_Model_Invitation_History extends Df_Core_Model_Abstract {
	/** @return string */
	public function getStatusText() {
		return
			Df_Invitation_Model_Source_Invitation_Status::s()
				->getOptionText(
					$this->getStatus()
				)
		;
	}

	/**
	 * @override
	 * @return Df_Invitation_Model_Invitation_History
	 */
	protected function _beforeSave() {
		$this->setDate($this->getResource()->formatDate(time()));
		parent::_beforeSave();
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Invitation_Model_Resource_Invitation_History::mf());
	}

	const _CLASS = __CLASS__;
	const P__ID = 'history_id';

	/** @return Df_Invitation_Model_Resource_Invitation_History_Collection */
	public static function c() {return self::s()->getCollection();}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Invitation_Model_Invitation_History
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @see Df_Invitation_Model_Resource_Invitation_History_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
	/** @return Df_Invitation_Model_Invitation_History */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}