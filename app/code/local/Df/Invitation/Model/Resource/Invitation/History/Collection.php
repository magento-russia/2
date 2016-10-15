<?php
class Df_Invitation_Model_Resource_Invitation_History_Collection
	extends Df_Core_Model_Resource_Collection {
	/**
	 * @override
	 * @return Df_Invitation_Model_Resource_Invitation_History
	 */
	public function getResource() {return Df_Invitation_Model_Resource_Invitation_History::s();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		$this->_itemObjectClass = Df_Invitation_Model_Invitation_History::class;
	}

}