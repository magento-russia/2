<?php
/** @return Df_Core_Helper_Data */
function df() {static $r; return $r ? $r : $r = Df_Core_Helper_Data::s();}

/** @return Df_Admin_Model_Settings */
function df_cfg() {return Df_Admin_Model_Settings::s();}

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

/** @return Df_Core_Helper_Df_Helper */
function df_h() {return Df_Core_Helper_Df_Helper::s();}

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

/**
 * @param bool $condition
 * @param mixed $resultOnTrue
 * @param mixed|null $resultOnFalse [optional]
 * @return mixed
 */
function df_if($condition, $resultOnTrue, $resultOnFalse = null) {
	return $condition ? $resultOnTrue : $resultOnFalse;
}

/**
 * @param Varien_Object|mixed[]|mixed $value
 * @return void
 */
function df_log($value) {Mage::log(df_dump($value));}

/** @return Df_Core_Helper_Mage */
function df_mage() {return Df_Core_Helper_Mage::s();}

/**
 * @param string $param1 [optional]
 * @param string $param2 [optional]
 * @return string|boolean
 */
function df_magento_version($param1 = null, $param2 = null) {
	return \Df\Core\Version::s()->get($param1, $param2);
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
 * @param mixed|string $value
 * @return mixed|null
 */
function df_n_get($value) {return 'df-null' === $value ? null : $value;}
/**
 * @param mixed|null $value
 * @return mixed|string
 */
function df_n_set($value) {return is_null($value) ? 'df-null' : $value;}

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
		\Df\Qa\Message\Notification::i(array(
			\Df\Qa\Message\Notification::P__NOTIFICATION => df_format($arguments)
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
	\Df\Qa\Message\Notification::i(array(
		\Df\Qa\Message\Notification::P__NOTIFICATION => $message
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
	\Df\Qa\Message\Failure\Exception::i(array(
		\Df\Qa\Message\Failure\Exception::P__EXCEPTION => $exception
		,\Df\Qa\Message\Failure\Exception::P__ADDITIONAL_MESSAGE => $additionalMessage
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
	\Df\Qa\Message\Notification::i(array(
		\Df\Qa\Message\Notification::P__NOTIFICATION => $message
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
 * При установке заголовка HTTP «Content-Type»
 * надёжнее всегда добавлять 3-й параметр: $replace = true,
 * потому что заголовок «Content-Type» уже ранее был установлен методом
 * @see Mage_Core_Model_App::getResponse()
 * @param Mage_Core_Controller_Response_Http $httpResponse
 * @param string $contentType
 * @return void
 */
function df_response_content_type(Mage_Core_Controller_Response_Http $httpResponse, $contentType) {
	$httpResponse->setHeader('Content-Type', $contentType, $replace = true);
}

/** @return Mage_Core_Model_Session|Df_Core_Model_Session|Mage_Adminhtml_Model_Session|Df_Core_Model_Session_Abstract */
function df_session() {
	/** @var Mage_Core_Model_Session_Abstract $result */
	static $result;
	if (!$result) {
		$result = df_is_admin() ? Mage::getSingleton('adminhtml/session') : df_session_core();
		df_assert($result instanceof Mage_Core_Model_Session_Abstract);
	}
	return $result;
}

/**
 * @param string $class
 * @return Object
 */
function df_singleton($class) {
	/** @var array(string => Object) $cache */
	static $cache = array();
	return isset($cache[$class]) ? $cache[$class] : $cache[$class] = new $class();
}

/** @return string */
function df_version() {
	/** @var string $result */
	static $result;
	if (!$result) {
		/** @var string $result */
		$result = df_leaf_sne(df_config_node('df/version'));
	}
	return $result;
}

/** @return string */
function df_version_full() {return sprintf('%s (%s)', df_version(), Mage::getVersion());}