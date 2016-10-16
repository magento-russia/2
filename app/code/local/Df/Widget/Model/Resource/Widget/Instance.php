<?php
class Df_Widget_Model_Resource_Widget_Instance extends Mage_Widget_Model_Resource_Widget_Instance {
	/**
	 * @param Mage_Core_Model_Abstract|Df_Widget_Model_Widget_Instance $object
	 * @return Df_Widget_Model_Resource_Widget_Instance
	 */
	protected function _afterSave(Mage_Core_Model_Abstract $object) {
		if ($object->needSaveRelations()) {
			parent::_afterSave($object);
		}
		return $this;
	}

	/** @return Df_Widget_Model_Resource_Widget_Instance */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}