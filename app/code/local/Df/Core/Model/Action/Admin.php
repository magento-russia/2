<?php
/** @method Df_Core_Controller_Admin getController() */
abstract class Df_Core_Model_Action_Admin extends Df_Core_Model_Action {
	/**
	 * После выполнения административной обработки,
	 * как правило, разумно перенаправлять администратора на тот экран,
	 * с которого он запускал обработку.
	 * @override
	 * @see Df_Core_Model_Action::getRedirectLocation()
	 * @used-by Df_Core_Model_Action::processRedirect()
	 * @return string
	 */
	protected function getRedirectLocation() {return self::$REDIRECT_LOCATION__REFERER;}

	/**
	 * Для административных обработок,
	 * как правило, разумнее добавлять диагностическое сообщение в сессию,
	 * чтобы оно отображалось в стандартном административном интерфейсе Magento,
	 * нежели вываливать его на белом экране.
	 * @override
	 * @see Df_Core_Model_Action::needRethrowException()
	 * @used-by Df_Core_Model_Action::needAddExceptionToSession()
	 * @used-by Df_Core_Model_Action::processException()
	 * @return bool
	 */
	protected function needRethrowException() {return false;}

	/**
	 * @override
	 * @see Df_Core_Model_Action::processFinish()
	 * @used-by Df_Core_Model_Action::process()
	 * @return void
	 */
	protected function processFinish() {
		df_admin_end();
		parent::processFinish();
	}

	/**
	 * @override
	 * @see Df_Core_Model_Action::processPrepare()
	 * @used-by Df_Core_Model_Action::process()
	 * @return void
	 */
	protected function processPrepare() {
		parent::processPrepare();
		df_admin_begin();
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__CONTROLLER, Df_Core_Controller_Admin::class);
	}
}