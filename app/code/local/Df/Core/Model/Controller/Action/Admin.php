<?php
/**
 * @method Df_Core_Controller_Admin getController()
 */
abstract class Df_Core_Model_Controller_Action_Admin extends Df_Core_Model_Controller_Action {
	/**
	 * @override
	 * @param string $path
	 * @param array $arguments[optional]
	 * @return Df_Core_Model_Controller_Action
	 */
	public function redirect($path, $arguments = array()) {
		$this->getController()->redirect($path, $arguments);
		return $this;
	}
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__CONTROLLER, Df_Core_Controller_Admin::_CLASS);
	}
}