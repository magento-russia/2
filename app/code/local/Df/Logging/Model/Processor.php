<?php
class Df_Logging_Model_Processor extends Df_Core_Model {
	/**
	 * Add new event changes
	 * @param Df_Logging_Model_Event_Changes $eventChange
	 * @return Df_Logging_Model_Processor
	 */
	public function addEventChanges($eventChange) {
		$this->_eventChanges[]= $eventChange;
		return $this;
	}
	/** @var Df_Logging_Model_Event_Changes[] */
	private $_eventChanges = array();

	/**
	 * Clear model data from objects, arrays and fields that should be skipped
	 * @deprecated after 1.6.0.0
	 * @param array $data
	 * @return array
	 */
	public function cleanupData($data) {
		if (!$data && !is_array($data)) {
			return array();
		}
		$clearData = array();
		foreach ($data as $key=>$value) {
			/** @noinspection PhpDeprecationInspection */
			/** @noinspection PhpDeprecationInspection */
			if (!in_array($key, $this->_skipFields) && !in_array($key, $this->_skipFieldsByModel) && !is_array($value) && !is_object($value)) {
				$clearData[$key] = $value;
			}
		}
		return $clearData;
	}

	/**
	 * @param Mage_Core_Model_Abstract $model
	 * @return Df_Logging_Model_Processor
	 */
	public function collectId(Mage_Core_Model_Abstract $model) {
		$this->_collectedIds[get_class($model)][]= $model->getId();
		return $this;
	}

	/** @return int[] */
	public function getCollectedIds() {
		$ids = array();
		foreach ($this->_collectedIds as $className => $classIds) {
			/**
			 * Нельзя здесь использовать @see array_unique_fast()
			 * вместо @see array_unique(),
			 * потому что значениями массива $classIds являются идентификаторы объектов,
			 * и среди них может быть значение null (для ещё не сохранявшихся в БД объектов),
			 * а @see array_unique_fast() не допускает в значениях массива null,
			 * потому что внутри себя использует функцию @see array_flip(),
			 * которая не допускает null в значениях массива:
			 * «Warning: array_flip(): Can only flip STRING and INTEGER values!».
			 * http://magento-forum.ru/topic/4600/
			 */
			/** @noinspection PhpParamsInspection */
			$uniqueIds = array_unique($classIds);
			$ids = array_merge($ids, $uniqueIds);
			$this->_collectedIds[$className] = $uniqueIds;
		}
		return $ids;
	}
	/** @var int[] */
	protected $_collectedIds = array();

	/**
	 * preDispatch action handler
	 * @param string $fullActionName Full action name like 'adminhtml_catalog_product_edit'
	 * @param string $actionName Action name like 'save', 'edit' etc.
	 */
	public function initAction($fullActionName, $actionName) {
		$this->_actionName = $actionName;
		if (!$this->_initAction){
			$this->_initAction = $fullActionName;
		}
		$this->_lastAction = $fullActionName;
		$this->_skipNextAction = (!Df_Logging_Model_Config::s()->isActive($fullActionName)) ? true : false;
		if ($this->_skipNextAction) {
			return;
		}
		$this->_eventConfig = Df_Logging_Model_Config::s()->getNode($fullActionName);
		/**
		 * Skip view action after save. For example on 'save and continue' click.
		 * Some modules always reloading page after save. We pass comma-separated list
		 * of actions into getSkipLoggingAction, it is necessary for such actions
		 * like customer balance, when customer balance ajax tab loaded after
		 * customer page.
		 */
		/** @var bool $doNotLog */
		$doNotLog = df_admin_session()->getSkipLoggingAction();
		if ($doNotLog) {
			if (is_array($doNotLog)) {
				/** @noinspection PhpParamsInspection */
				$key = array_search($fullActionName, $doNotLog);
				if ($key !== false) {
					unset($doNotLog[$key]);
					df_admin_session()->setSkipLoggingAction($doNotLog);
					$this->_skipNextAction = true;
					return;
				}
			}
		}
		if (isset($this->_eventConfig->{'skip_on_back'})) {
			$addValue = array_keys($this->_eventConfig->{'skip_on_back'}->asArray());
			$sessionValue = df_admin_session()->getSkipLoggingAction();
			if (!is_array($sessionValue) && $sessionValue) {
				$sessionValue = df_csv_parse($sessionValue);
			}
			else if (!$sessionValue) {
				$sessionValue = array();
			}
			$merge = array_merge($addValue, $sessionValue);
			df_admin_session()->setSkipLoggingAction($merge);
		}
	}

	/**
	 * Postdispatch action handler
	 * @return void
	 */
	public function logAction() {
		if (!$this->_initAction) {
			return;
		}
		/** @var string|null $username */
		$username = df_admin_name(false);
		/** @var int|null $userId */
		$userId = df_admin_id(false);
		$errors = df_model('adminhtml/session')->getMessages()->getErrors();
		$loggingEvent = df_model('df_logging/event')->setData(array(
			'ip' => df_visitor_ip()
			,'x_forwarded_ip' => Mage::app()->getRequest()->getServer('HTTP_X_FORWARDED_FOR')
			,'user' => $username
			,'user_id' => $userId
			,'is_success' => empty($errors)
			,'fullaction' => $this->_initAction
			,'error_message' => df_cc_n(array_map(
				create_function('$a', 'return $a->toString();')
				,$errors
			))
		));
		if ($this->_actionName == 'denied') {
			$_conf = Df_Logging_Model_Config::s()->getNode($this->_initAction);
			if (!$_conf || !Df_Logging_Model_Config::s()->isActive($this->_initAction)) {
				return;
			}
			$loggingEvent->setAction($_conf->action);
			$loggingEvent->setEventCode($_conf->getParent()->getParent()->getName());
			$loggingEvent->setInfo(Df_Logging_Helper_Data::s()->__('Access denied'));
			$loggingEvent->setIsSuccess(0);
			$loggingEvent->save();
			return;
		}
		if ($this->_skipNextAction) {
			return;
		}
		$loggingEvent->setAction($this->_eventConfig->{'action'});
		$loggingEvent->setEventCode($this->_eventConfig->getParent()->getParent()->getName());
		try {
			$callback = isset($this->_eventConfig->{'post_dispatch'}) ? (string)$this->_eventConfig->{'post_dispatch'} : false;
			$defaulfCallback = 'postDispatchGeneric';
			$classMap = $this->_getCallbackFunction($callback, $this->_controllerActionsHandler, $defaulfCallback);
			$handler  = $classMap['handler'];
			$callback = $classMap['callback'];
			if (!$handler) {
				return;
			}
			if ($handler->$callback($this->_eventConfig, $loggingEvent, $this)) {
				$loggingEvent->save();
				$eventId = $loggingEvent->getId();
				if ($eventId) {
					foreach ($this->_eventChanges as $changes){
						if ($changes && ($changes->getOriginalData() || $changes->getResultData())) {
							$changes->setEventId($eventId);
							$changes->save();
						}
					}
				}
			}
		} catch (Exception $e) {
			df_handle_entry_point_exception($e, false);
		}
	}

	/**
	 * Action model processing.
	 * Get defference between data & orig_data and store in the internal modelsHandler container.
	 *
	 * @param Varien_Object $model
	 * @param $action
	 * @return void
	 */
	public function modelActionAfter($model, $action) {
		if ($this->_skipNextAction) {
			return;
		}
		//These models used when we merge action models with action group models
		$usedModels = $defaultExpectedModels = null;
		if ($this->_eventConfig) {
			$actionGroupNode = $this->_eventConfig->getParent()->getParent();
			if (isset($actionGroupNode->expected_models)) {
				$defaultExpectedModels = $actionGroupNode->expected_models;
			}
		}

		//Exact models in exactly action node
		$expectedModels = isset($this->_eventConfig->{'expected_models'})
			? $this->_eventConfig->{'expected_models'} : false;
		if (!$expectedModels || empty($expectedModels)) {
			if (empty($defaultExpectedModels)) {
				return;
			}
			$usedModels = $defaultExpectedModels;
		}
		else {
			if ($expectedModels->getAttribute('extends') == 'merge') {
				$defaultExpectedModels->extend($expectedModels);
				$usedModels = $defaultExpectedModels;
			}
			else {
				$usedModels = $expectedModels;
			}
		}

		$skipData = array();
		//Log event changes for each model
		foreach ($usedModels->children() as $expect => $callback) {
			//Add custom skip fields per expecetd model
			if (isset($callback->skip_data)) {
				if ($callback->skip_data->hasChildren()) {
					foreach ($callback->skip_data->children() as $skipName => $skipObj) {
						if (!in_array($skipName, $skipData)) {
							$skipData[]= $skipName;
						}
					}
				}
			}
			$className = Mage::getConfig()->getModelClassName(str_replace('__', '/', $expect));
			if ($model instanceof $className){
				/**
				 * Намеренно используем @uses ucfirst() вместо @see df_ucfirst()
				 * потому что в данном случае нам не нужна поддержка UTF-8.
				 */
				$classMap = $this->_getCallbackFunction(
					trim($callback), $this->_modelsHandler, 'model' . ucfirst($action) . 'After'
				);
				$handler  = $classMap['handler'];
				$callback = $classMap['callback'];
				if ($handler) {
					if (
						$model instanceof Mage_Core_Model_Store
						|| df_cfgr()->logging()->isEnabled()
					) {
						$changes = $handler->$callback($model, $this);
						//Because of logging view action, $changes must be checked if it is an object
						if (is_object($changes)) {
							$changes->cleanupData($skipData);
							if ($changes->hasDifference()) {
								$changes->setSourceName($className);
								$changes->setSourceId($model->getId());
								$this->addEventChanges($changes);
							}
						}

					}

				}
			}
			$skipData = array();
		}
	}

	/**
	 * Get callback function for logAction and modelActionAfter functions
	 * @param string $srtCallback
	 * @param Varien_Object $defaultHandler
	 * @param string $defaultFunction
	 * @return array Contains two values 'handler' and 'callback' that indicate what callback function should be applied
	 */
	private function _getCallbackFunction($srtCallback, $defaultHandler, $defaultFunction) {
		$return = array('handler' => $defaultHandler, 'callback' => $defaultFunction);
		if (empty($srtCallback)) {
			return $return;
		}
		try {
			$classPath = explode('::', $srtCallback);
			if (2 === count($classPath)) {
				$return['handler'] = Mage::getSingleton(str_replace('__', '/', $classPath[0]));
				$return['callback'] = $classPath[1];
			} else {
				$return['callback'] = $classPath[0];
			}
			if (!$return['handler'] || !$return['callback'] || !method_exists($return['handler'],$return['callback'])) {
				Mage::throwException("Unknown callback function: {$srtCallback}");
			}
		} catch (Exception $e) {
			$return['handler'] = false;
			df_handle_entry_point_exception($e, false);
		}
		return $return;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		/**
		 * http://magento-forum.ru/topic/4485/
		 * Видимо, сбой «Call to undefined function df_model»
		 * происходит при выполнении стороннего скрипта,
		 * который написан некачественно и не вызывает системные события.
		 */
		Df_Core_Boot::run();
		$this->_modelsHandler = df_model('df_logging/handler_models');
		$this->_controllerActionsHandler = df_model('df_logging/handler_controllers');
	}
	/** @var Varien_Simplexml_Element */
	private $_eventConfig;
	private $_controllerActionsHandler;
	private $_modelsHandler;
	/** @var string */
	private $_actionName = '';
	/** @var string */
	private $_lastAction = '';
	/** @var string */
	private $_initAction = '';
	/** @var bool */
	private $_skipNextAction = false;
	/**
	 * Set of fields that should not be logged for all models
	 * @deprecated 1.6.0.0
	 * @var mixed[]
	 */
	protected $_skipFields = array();
	/**
	 * Set of fields that should not be logged per expected model
	 * @deprecated 1.6.0.0
	 * @var mixed[]
	 */
	protected $_skipFieldsByModel = array();


	/**
	 * @deprecated after 1.6.0.0
	 */
	const XML_PATH_SKIP_GLOBAL_FIELDS = 'adminhtml/df/logging/skip_fields';

	/** @return Df_Logging_Model_Processor */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}