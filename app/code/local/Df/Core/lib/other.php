<?php
/** @return Df_Core_Helper_Data */
function df() {static $r; return $r ? $r : $r = Df_Core_Helper_Data::s();}

/** @return Df_Admin_Model_Settings */
function df_cfg() {return Df_Admin_Model_Settings::s();}

/** @return string */
function df_current_url() {return df_mage()->core()->url()->getCurrentUrl();}

/**
 * @param mixed $value
 * @return bool
 */
function df_empty_string($value) {return '' === $value;}

/**
 * К сожалению, не можем перекрыть Exception::getTraceAsString(),
 * потому что этот метод — финальный
 *
 * @param Exception $exception
 * @param bool $showCodeContext [optional]
 * @return string
 */
function df_exception_get_trace(Exception $exception, $showCodeContext = false) {
	return Df_Qa_Message_Failure_Exception::i(array(
		Df_Qa_Message_Failure_Exception::P__EXCEPTION => $exception
		,Df_Qa_Message_Failure_Exception::P__NEED_LOG_TO_FILE => false
		,Df_Qa_Message_Failure_Exception::P__NEED_NOTIFY_DEVELOPER => false
		,Df_Qa_Message_Failure_Exception::P__SHOW_CODE_CONTEXT => $showCodeContext
	))->traceS();
}

/**
 * Обработка исключительных ситуаций в точках сочленения моих модулей и ядра
 *
 * ($rethrow === true) => перевозбудить исключительную ситуацию
 * ($rethrow === false) => не перевозбуждать исключительную ситуацию
 * ($rethrow === null) =>  перевозбудить исключительную ситуацию, если включен режим разработчика
 *
 * @param Exception $e
 * @param bool|null $rethrow
 * @param bool|null $sendContentTypeHeader
 * @throws Exception
 * @return void
 */
function df_handle_entry_point_exception(Exception $e, $rethrow = null, $sendContentTypeHeader = true) {
	/**
	 * Надо учесть, что исключительная ситуация могла произойти при асинхронном запросе,
	 * и в такой ситуации echo() неэффективно.
	 */
	df_notify_exception($e);
	/**
	 * В режиме разработчика
	 * по умолчанию выводим диагностическое сообщение на экран
	 * (но это можно отключить посредством $rethrow = false).
	 *
	 * При отключенном режиме разработчика
	 * по умолчанию не выводим диагностическое сообщение на экран
	 * (но это можно отключить посредством $rethrow = true).
	 */
	if (Mage::getIsDeveloperMode() && false !== $rethrow || true === $rethrow) {
		/**
		 * Чтобы кириллица отображалась в верной кодировке —
		 * пробуем отослать браузеру заголовок Content-Type.
		 *
		 * Обратите внимание, что такой подход не всегда корректен:
		 * ведь нашу исключительную ситуацию может поймать и обработать
		 * ядро Magento или какой-нибудь сторонний модуль, и они затем могут
		 * захотеть вернуть браузеру документ другого типа (не text/html).
		 * Однако, по-правильному они должны при этом сами установить свой Content-type.
		 */
		if (!headers_sent() && $sendContentTypeHeader) {
			header('Content-Type: text/html; charset=UTF-8');
		}
		throw $e;
	}
}

/**
 * @static
 * @param string|string[] $handlerClass
 * @param string $eventClass
 * @param Varien_Event_Observer $observer
 * @return void
 */
function df_handle_event($handlerClass, $eventClass, Varien_Event_Observer $observer) {
	/** @var Df_Core_Model_Event $event */
	$event = Df_Core_Model_Event::create($eventClass, $observer);
	if (!is_array($handlerClass)) {
		Df_Core_Model_Handler::create($handlerClass, $event)->handle();
	}
	else {
		foreach ($handlerClass as $handlerClassItem) {
			/** @var string $handlerClassItem */
			Df_Core_Model_Handler::create($handlerClassItem, $event)->handle();
		}
	}
}

/** @return Df_Core_Helper_Df_Helper */
function df_h() {return Df_Core_Helper_Df_Helper::s();}

/** @return Df_Core_Helper_Mage */
function df_mage() {return Df_Core_Helper_Mage::s();}

/**
 * @param string $param1 [optional]
 * @param string $param2 [optional]
 * @return string|boolean
 */
function df_magento_version($param1 = null, $param2 = null) {
	return df()->version()->magentoVersion($param1, $param2);
}

/**
 * @param string $moduleName
 * @return bool
 */
function df_module_enabled($moduleName) {
	/** @var Df_Core_Model_Cache_Module $c */
	static $c; if(!$c) {$c = Df_Core_Model_Cache_Module::s();}
	return $c->isEnabled($moduleName);
}

/**
 * @param mixed $argument
 * @return mixed
 */
function df_nop($argument) {return $argument;}

/**
 * @param string|Exception $message
 * @return void
 */
function df_notify($message) {
	if ($message instanceof Exception) {
		df_notify_exception($message);
	}
	else {
		/** @var mixed[] $arguments */
		$arguments = func_get_args();
		Df_Qa_Message_Notification::i(array(
			Df_Qa_Message_Notification::P__NOTIFICATION => df_format($arguments)
			,Df_Qa_Message_Notification::P__NEED_LOG_TO_FILE => true
			,Df_Qa_Message_Notification::P__NEED_NOTIFY_DEVELOPER => true
		))->log();
	}
}

/**
 * @param string $message
 * @param bool $doLog [optional]
 * @return void
 */
function df_notify_admin($message, $doLog = true) {
	if (is_string($doLog)) {
		$doLog = true;
		/** @var mixed[] $arguments */
		$arguments = func_get_args();
		$message = df_format($arguments);
	}
	Df_Qa_Message_Notification::i(array(
		Df_Qa_Message_Notification::P__NOTIFICATION => $message
		,Df_Qa_Message_Notification::P__NEED_LOG_TO_FILE => $doLog
		,Df_Qa_Message_Notification::P__FILE_NAME => 'rm.admin.log'
		,Df_Qa_Message_Notification::P__NEED_NOTIFY_ADMIN => true
		,Df_Qa_Message_Notification::P__NEED_NOTIFY_DEVELOPER => false
	))->log();
}

/**
 * Задача данного метода — ясно и доступно объяснить разработчику причину исключительной ситуации
 * и состояние системы в момент возникновения исключительной ситуации.
 * @param Exception|string $exception
 * @param string|null $additionalMessage [optional]
 * @return void
 */
function df_notify_exception($exception, $additionalMessage = null) {
	if (is_string($exception)) {
		$exception = new Exception($exception);
	}
	Df_Qa_Message_Failure_Exception::i(array(
		Df_Qa_Message_Failure_Exception::P__EXCEPTION => $exception
		,Df_Qa_Message_Failure_Exception::P__ADDITIONAL_MESSAGE => $additionalMessage
		,Df_Qa_Message_Failure_Exception::P__NEED_LOG_TO_FILE => true
		,Df_Qa_Message_Failure_Exception::P__NEED_NOTIFY_DEVELOPER => true
	))->log();
}

/**
 * @param string $message
 * @param bool $doLog [optional]
 * @return void
 */
function df_notify_me($message, $doLog = true) {
	if (is_string($doLog)) {
		$doLog = true;
		/** @var mixed[] $arguments */
		$arguments = func_get_args();
		$message = df_format($arguments);
	}
	Df_Qa_Message_Notification::i(array(
		Df_Qa_Message_Notification::P__NOTIFICATION => $message
		,Df_Qa_Message_Notification::P__NEED_LOG_TO_FILE => $doLog
		,Df_Qa_Message_Notification::P__FILE_NAME => 'rm.developer.log'
		,Df_Qa_Message_Notification::P__NEED_NOTIFY_DEVELOPER => true
	))->log();
}

/**
 * @param mixed|null $value
 * @param bool $skipEmptyCheck [optional]
 * @return mixed[]
 */
function df_nta($value, $skipEmptyCheck = false) {
	if (!is_array($value)) {
		if (!$skipEmptyCheck) {
			df_assert(empty($value));
		}
		$value = array();
	}
	return $value;
}

/**
 * @param mixed|null $value
 * @return mixed
 */
function df_nts($value) {return !is_null($value) ? $value : '';}

/** @return Df_Core_Helper_Output */
function df_output() {return Df_Core_Helper_Output::s();}

/**
 * @param float|int $value
 * @return int
 */
function df_round($value) {return (int)round($value);}

/** @return Df_Core_Helper_Url */
function df_url() {return Df_Core_Helper_Url::s();}

/**
 * @param Varien_Object $object
 * @param string[] $absentProperties [optional]
 * @return Varien_Object
 */
function rm_adapt_legacy_object(Varien_Object $object, $absentProperties = array()) {
	foreach ($object->getData() as $key => $value) {
		/** @var string $key */
		/** @var mixed $value */
		if (!isset($object->$key)) {
			$object->$key = $value;
		}
	}
	/**
	 * Позволяет инициализировать те поля, которые отсутствуют в @uses Varien_Object::getData(),
	 * но обращение к которым, тем не менее, происходит в дефектных оформительских темах.
	 */
	if ($absentProperties) {
		foreach ($absentProperties as $absentProperty) {
			/** @var string $absentProperty */
			if (!isset($object->$absentProperty)) {
				$object->$absentProperty = $object->getData($absentProperty);
			}
		}
	}
	return $object;
}

/** @return void */
function rm_admin_begin() {Df_Admin_Model_Mode::s()->begin();}

/**
 * @param object $object
 * @param string $method
 * @param array(string => mixed) $parameters [optional]
 * @return mixed
 * @throws Exception
 */
function rm_admin_call($object, $method, array $parameters = array()) {
	Df_Admin_Model_Mode::s()->call($object, $method, $parameters);
}

/** @return void */
function rm_admin_end() {Df_Admin_Model_Mode::s()->end();}

/**
 * @param float|int $value
 * @return int
 */
function rm_ceil($value) {return (int)ceil($value);}

/**
 * @see rm_encrypt()
 * @param $value
 * @return string
 */
function rm_decrypt($value) {return df_mage()->coreHelper()->decrypt($value);}

/**
 * @param mixed $value
 * @return mixed
 */
function rm_empty_to_null($value) {return $value ? $value : null;}

/**
 * @see rm_decrypt()
 * @param $value
 * @return string
 */
function rm_encrypt($value) {return df_mage()->coreHelper()->encrypt($value);}

/**
 * @param float|int $value
 * @return int
 */
function rm_floor($value) {return (int)floor($value);}

/**
 * @param bool $condition
 * @param mixed $resultOnTrue
 * @param mixed|null $resultOnFalse [optional]
 * @return mixed
 */
function rm_if($condition, $resultOnTrue, $resultOnFalse = null) {
	return $condition ? $resultOnTrue : $resultOnFalse;
}

/**
 * @param Varien_Object|mixed[]|mixed $value
 * @return void
 */
function rm_log($value) {Mage::log(df_dump($value));}

/**
 * При установке заголовка HTTP «Content-Type»
 * надёжнее всегда добавлять 3-й параметр: $replace = true,
 * потому что заголовок «Content-Type» уже ранее был установлен методом
 * @see Mage_Core_Model_App::getResponse()
 * @param Mage_Core_Controller_Response_Http $httpResponse
 * @param string $contentType
 * @return void
 */
function rm_response_content_type(Mage_Core_Controller_Response_Http $httpResponse, $contentType) {
	$httpResponse->setHeader('Content-Type', $contentType, $replace = true);
}

/**
 * Обратите внимание, что для формирования веб-адресов
 * мы не можем с целью ускорения использовать объект-одиночку класса @see Mage_Core_Model_Url,
 * потому что оба передаваемых для формирования веб-адреса параметра
 * влияют на объект @see Mage_Core_Model_Url:
 *
 * Второй параметр, $routeParams, влияет непосредственно в методе @see Mage_Core_Model_Url::getUrl().
 * Первый параметр, $routePath, влияет через вызов из @see Mage_Core_Model_Url::getUrl():
 * $url = $this->getRouteUrl($routePath, $routeParams);
 * @see Mage_Core_Model_Url::getRouteUrl
 *
 * Обратите внимание, что если в качестве $routeParams передать параметры запроса в виде
 * array('paramName' => 'paramValue'),
 * то в веб-адресе эти параметры будут содержаться через косую черту: paramName/paramValue.
 *
 * Если же в качестве $routeParams передать конструкцию вида
 * array('_query' => array('paramName' => 'paramValue')),
 * то в веб-адресе эти параметры будут содержаться стандартным для PHP способом:
 * ?paramName=paramValue.
 * Пример: @see Df_Payment_Model_Method_WithRedirect::getCustomerReturnUrl()
 *
 * В Magento в большинстве случаев оба варианта равноценны, но надо понимать разницу между ними!
 *
 * @param string $routePath
 * @param array(string => mixed) $routeParams [optional]
 * @return string
 */
function rm_url($routePath, array $routeParams = array()) {
	/** @var Df_Core_Model_Url $url */
	$url = new Df_Core_Model_Url();
	return $url->getUrl($routePath, $routeParams);
}

/**
 * Обратите внимание, что для формирования веб-адресов
 * мы не можем с целью ускорения использовать объект-одиночку класса @see Mage_Adminhtml_Model_Url,
 * потому что оба передаваемых для формирования веб-адреса параметра
 * влияют на объект @see Mage_Adminhtml_Model_Url:
 *
 * Второй параметр, $routeParams,
 * влияет непосредственно в методе @see Mage_Adminhtml_Model_Url::getUrl().
 * Первый параметр, $routePath, влияет через вызов из @see Mage_Adminhtml_Model_Url::getUrl():
 * $url = $this->getRouteUrl($routePath, $routeParams);
 * @see Mage_Core_Model_Url::getRouteUrl
 *
 * Обратите внимание, что если в качестве $routeParams передать параметры запроса в виде
 * array('paramName' => 'paramValue'),
 * то в веб-адресе эти параметры будут содержаться через косую черту: paramName/paramValue.
 *
 * Если же в качестве $routeParams передать конструкцию вида
 * array('_query' => array('paramName' => 'paramValue')),
 * то в веб-адресе эти параметры будут содержаться стандартным для PHP способом:
 * ?paramName=paramValue.
 * Пример: @see Df_Payment_Model_Method_WithRedirect::getCustomerReturnUrl()
 *
 * В Magento в большинстве случаев оба варианта равноценны, но надо понимать разницу между ними!
 * @used-by rm_admin_button_location()
 * @param string $routePath
 * @param array(string => mixed) $routeParams [optional]
 * @return string
 */
function rm_url_admin($routePath, array $routeParams = array()) {
	/** @var Mage_Adminhtml_Model_Url $url */
	$url = Mage::getModel('adminhtml/url');
	return $url->getUrl($routePath, $routeParams);
}
/**
 * Оказывается, что нельзя писать
 * const RM_NULL = 'rm-null';
 * потому что глобальные константы появились только в PHP 5.3.
 * http://www.codingforums.com/php/303927-unexpected-t_const-php-version-5-2-17-a.html#post1363452
 */
define('RM_NULL', 'rm-null');

/**
 * @param mixed|string $value
 * @return mixed|null
 */
function rm_n_get($value) {return (RM_NULL === $value) ? null : $value;}
/**
 * @param mixed|null $value
 * @return mixed|string
 */
function rm_n_set($value) {return is_null($value) ? RM_NULL : $value;}

/**
 * @param float|int $value
 * @return int
 */
function rm_round($value) {return (int)round($value);}

/** @return Mage_Core_Model_Session_Abstract */
function rm_session() {
	/** @var Mage_Core_Model_Session_Abstract $result */
	static $result;
	if (!$result) {
		$result = df_is_admin() ? Mage::getSingleton('adminhtml/session') : rm_session_core();
		df_assert($result instanceof Mage_Core_Model_Session_Abstract);
	}
	return $result;
}

/**
 * @param string $class
 * @return Object
 */
function rm_singleton($class) {
	/** @var array(string => Object) $cache */
	static $cache = array();
	return isset($cache[$class]) ? $cache[$class] : $cache[$class] = new $class();
}

/** @return string */
function rm_version() {
	/** @var string $result */
	static $result;
	if (!$result) {
		/** @var string $result */
		$result = rm_leaf_sne(rm_config_node('df/version'));
	}
	return $result;
}

/** @return string */
function rm_version_full() {return sprintf('%s (%s)', rm_version(), Mage::getVersion());}