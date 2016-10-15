<?php
class Df_Logging_Observer extends Df_Core_Model {
	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @see Mage_Admin_Model_Session::login()
	 	Mage::dispatchEvent('admin_session_user_login_success', array('user' => $user));
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function admin_session_user_login_success(Varien_Event_Observer $o) {
		if (self::enabled()) {
			/** @var Mage_Admin_Model_User $user */
			$user = $o['user'];
			$this->_logAdminLogin($user->getUsername(), $user->getId());
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @see Mage_Core_Controller_Varien_Action::postDispatch()
		Mage::dispatchEvent(
			'controller_action_postdispatch_'.$this->getRequest()->getRouteName(),
			array('controller_action'=>$this)
		);
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function controller_action_postdispatch_adminhtml(Varien_Event_Observer $o) {
		if (self::enabled()) {
			/** @var Mage_Core_Controller_Varien_Action $controller */
			$controller = $o['controller_action'];
			if ($controller->getRequest()->isDispatched()) {
				Df_Logging_Model_Processor::s()->logAction();
			}
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function controller_action_predispatch(Varien_Event_Observer $o) {
		if (self::enabled()) {
			/** @var Mage_Core_Controller_Varien_Action $controller */
			$controller = $o['controller_action'];
			/* @var Mage_Core_Controller_Request_Http $request */
			$request = $controller->getRequest();
			$beforeForwardInfo = $request->getBeforeForwardInfo();
			// Always use current action name bc basing on
			// it we make decision about access granted or denied
			$actionName = $request->getRequestedActionName();
			if (empty($beforeForwardInfo)) {
				$fullActionName = $controller->getFullActionName();
			} else {
				$fullActionName = array($request->getRequestedRouteName());
				if (isset($beforeForwardInfo['controller_name'])) {
					$fullActionName[]= $beforeForwardInfo['controller_name'];
				} else {
					$fullActionName[]= $request->getRequestedControllerName();
				}
				if (isset($beforeForwardInfo['action_name'])) {
					$fullActionName[]= $beforeForwardInfo['action_name'];
				} else {
					$fullActionName[]= $actionName;
				}
				$fullActionName = implode('_', $fullActionName);
			}
			Df_Logging_Model_Processor::s()->initAction($fullActionName, $actionName);
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function model_delete_after(Varien_Event_Observer $o) {
		if (df_cfg()->logging()->isEnabled()) {
			Df_Logging_Model_Processor::s()->modelActionAfter($o['object'], 'delete');
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function model_load_after(Varien_Event_Observer $o) {
		Df_Logging_Model_Processor::s()->modelActionAfter($o['object'], 'view');
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function model_save_after(Varien_Event_Observer $o) {
		if (df_cfg()->logging()->isEnabled()) {
			Df_Logging_Model_Processor::s()->modelActionAfter($o['object'], 'save');
		}
	}

	/**
	 * @used-by Mage_Cron_Model_Observer::_processJob()
	 * @return void
	 */
	public function rotateLogs() {
		if (self::enabled()) {
			$lastRotationFlag = df_model('df_logging/flag')->loadSelf();
			//$lastRotationTime = $lastRotationFlag->getFlagData();
	//		$rotationFrequency =
	//			3600 * 24 * df_cfg()->admin()->logging()->archiving()->getFrequency()
	//		;
	//		if (!$lastRotationTime || ($lastRotationTime < time() - $rotationFrequency)) {
			Df_Logging_Model_Resource_Event::s()->rotate(
				3600 * 24 * (df_cfg()->admin()->logging()->archiving()->getLifetime())
			);
	//		}
			$lastRotationFlag->setFlagData(time())->save();
		}
	}

	/**
	 * Log sign in attempt
	 * @param string $username
	 * @param int $userId
	 * @return void
	 */
	private function _logAdminLogin($username, $userId = null) {
		$eventCode = 'admin_login';
		if (!Df_Logging_Model_Config::s()->isActive($eventCode, true)) {
			return;
		}
		$success = !!$userId;
		if (!$userId) {
			$userId = Mage::getSingleton('admin/user')->loadByUsername($username)->getId();
		}
		$request = Mage::app()->getRequest();
		/** @var Df_Logging_Model_Event $result */
		$result = Df_Logging_Model_Event::s();
		$result->setData(array(
			'ip' => df_visitor_ip()
			,'user' => $username
			,'user_id' => $userId
			,'is_success' => $success
			,'fullaction' => "{$request->getRouteName()}_{$request->getControllerName()}_{$request->getActionName()}"
			,'event_code' => $eventCode
			,'action' => 'login'
		));
		$result->save();
	}

	/** @return bool */
	private static function enabled() {
		/** @var bool $result */
		static $result;
		if (is_null($result)) {
			$result = df_cfg()->logging()->isEnabled();
		}
		return $result;
	}
}