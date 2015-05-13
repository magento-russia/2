<?php
/**
 * Log admin actions and performed changes.
 * It doesn't log all admin actions, only listed in logging.xml config files.
 */
class Df_Logging_Model_Observer extends Df_Core_Model_Abstract {
	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function adminSessionLoginFailed($observer) {
		if (
				df_cfg()->logging()->isEnabled()
			&&
				df_enabled(Df_Core_Feature::LOGGING)
		) {
			$eventModel = $this->_logAdminLogin($observer->getUserName());
			if (class_exists('Df_Pci_Model_Observer', false) && $eventModel) {
				$exception = $observer->getException();
				if ($exception->getCode() == Df_Pci_Model_Observer::ADMIN_USER_LOCKED) {
					$eventModel->setInfo(Df_Logging_Helper_Data::s()->__('User is locked'))->save();
				}
			}
		}
	}

	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function adminSessionLoginSuccess($observer) {
		if (
				df_cfg()->logging()->isEnabled()
			&&
				df_enabled(Df_Core_Feature::LOGGING)
		) {
			$this->_logAdminLogin($observer->getUser()->getUsername(), $observer->getUser()->getId());
		}
	}

	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function controllerPostdispatch($observer) {
		if (
				df_cfg()->logging()->isEnabled()
			&&
				df_enabled(Df_Core_Feature::LOGGING)
		) {
			if ($observer->getEvent()->getControllerAction()->getRequest()->isDispatched()) {
				Df_Logging_Model_Processor::s()->logAction();
			}
		}
	}

	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function controllerPredispatch($observer) {
		if (
				df_cfg()->logging()->isEnabled()
			&&
				df_enabled(Df_Core_Feature::LOGGING)
		) {
			/* @var $action Mage_Core_Controller_Varien_Action */
			$action = $observer->getEvent()->getControllerAction();
			/* @var $request Mage_Core_Controller_Request_Http */
			$request = $observer->getEvent()->getControllerAction()->getRequest();
			$beforeForwardInfo = $request->getBeforeForwardInfo();
			// Always use current action name bc basing on
			// it we make decision about access granted or denied
			$actionName = $request->getRequestedActionName();
			if (empty($beforeForwardInfo)) {
				$fullActionName = $action->getFullActionName();
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
	 * @param Varien_Event_Observer
	 * @return void
	 */
	public function modelSaveAfter($observer) {
		if (df_cfg()->logging()->isEnabled()) {
			/**
			 * Опасно здесь ставить df_enabled, потому что можем попасть в рекурсию.
			 * Вместо этого ставим df_enabled внутри Df_Logging_Model_Processor::s()->modelActionAfter
			 */
			Df_Logging_Model_Processor::s()->modelActionAfter($observer->getEvent()->getObject(), 'save');
		}
	}

	/**
	 * @return void
	 * @param Varien_Event_Observer
	 */
	public function modelDeleteAfter($observer) {
		if (df_cfg()->logging()->isEnabled()) {
			/**
			 * Опасно здесь ставить df_enabled, потому что можем попасть в рекурсию.
			 * Вместо этого ставим df_enabled внутри Df_Logging_Model_Processor::s()->modelActionAfter
			 */
			Df_Logging_Model_Processor::s()->modelActionAfter($observer->getEvent()->getObject(), 'delete');
		}
	}

	/**
	 * @return void
	 * @param Varien_Event_Observer
	 */
	public function modelLoadAfter($observer) {
		/**
		 * Нельзя здесь ставить df_enabled или df_cfg()->logging()->isEnabled(),
		 * потому что иначе попадём в рекурсию.
		 * Вместо этого выполняем проверки внутри Df_Logging_Model_Processor::s()->modelActionAfter
		 */
		Df_Logging_Model_Processor::s()->modelActionAfter($observer->getEvent()->getObject(), 'view');
	}

	/**
	 * Cron job for logs rotation
	 * @return void
	 */
	public function rotateLogs() {
		if (
				df_cfg()->logging()->isEnabled()
			&&
				df_enabled(Df_Core_Feature::LOGGING)
		) {
			$lastRotationFlag = df_model('df_logging/flag')->loadSelf();
			//$lastRotationTime = $lastRotationFlag->getFlagData();
	//		$rotationFrequency =
	//			3600 * 24 * df_cfg()->admin()->logging()->archiving()->getFrequency()
	//		;
	//		if (!$lastRotationTime || ($lastRotationTime < time() - $rotationFrequency)) {
				Mage::getResourceModel('df_logging/event')->rotate(
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
	 * @return Df_Logging_Model_Event
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
			'ip' => Mage::helper('core/http')->getRemoteAddr()
			,'user' => $username
			,'user_id' => $userId
			,'is_success' => $success
			,'fullaction' => "{$request->getRouteName()}_{$request->getControllerName()}_{$request->getActionName()}"
			,'event_code' => $eventCode
			,'action' => 'login'
		));
		$result->save();
		return $result;
	}
}