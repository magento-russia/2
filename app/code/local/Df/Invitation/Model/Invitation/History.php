<?php
class Df_Invitation_Model_Invitation_History extends Df_Core_Model {
	/**
	 * @override
	 * @return Df_Invitation_Model_Resource_Invitation_History_Collection
	 */
	public function getResourceCollection() {return self::c();}

	/** @return string */
	public function getStatusText() {
		return Df_Invitation_Model_Source_Invitation_Status::s()->getOptionText($this->getStatus());
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
	 * @return Df_Invitation_Model_Resource_Invitation_History
	 */
	protected function _getResource() {return Df_Invitation_Model_Resource_Invitation_History::s();}

	/** @used-by Df_Invitation_Model_Resource_Invitation_History_Collection::_construct() */

	const P__ID = 'history_id';

	/** @return Df_Invitation_Model_Resource_Invitation_History_Collection */
	public static function c() {return new Df_Invitation_Model_Resource_Invitation_History_Collection;}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Invitation_Model_Invitation_History
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}