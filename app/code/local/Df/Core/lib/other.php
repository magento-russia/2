<?php
/** @return Df_Core_Helper_Data */
function df() {
	// метод реализован именно таким способом ради ускорения
	static $r; return $r ? $r : $r = Df_Core_Helper_Data::s();
}

/**
 * В качестве параметра $type можно передавать:
 * 1) объект-блок
 * 2) класс блока в стандартном формате
 * 3) класс блока в формате Magento
 * @param string|Mage_Core_Block_Abstract $type
 * @param string $name
 * @param mixed[]|string $attributes
 * @return Mage_Core_Block_Abstract|bool
 */
function df_block($type, $name = '', $attributes = array()) {
	if (is_string($attributes)) {
		$attributes = array(Df_Core_Block_Template::P__TEMPLATE => $attributes);
	}
	df_param_array($attributes, 2);
	if (is_object($type)) {
		/**
		 * @see Mage_Core_Model_Layout::createBlock() не добавит параметры к блоку,
		 * если в этот метод передать не тип блока, а еще созданный объект-блок.
		 */
		df_assert($type instanceof Mage_Core_Block_Abstract);
		$type->addData($attributes);
	}
	/** @var Mage_Core_Block_Abstract $result */
	$result = rm_layout()->createBlock($type, $name, $attributes);
	df_assert($result instanceof Mage_Core_Block_Abstract);
	return $result;
}

/**
 * В качестве параметра $type можно передавать:
 * 1) объект-блок
 * 2) класс блока в стандартном формате
 * 3) класс блока в формате Magento
 * @param string|Mage_Core_Block_Abstract $type
 * @param string $name
 * @param mixed[]|string $attributes
 * @return string
 */
function df_block_render($type, $name = '', $attributes = array()) {
	return df_block($type, $name, $attributes)->toHtml();
}

/**
 * @param mixed[]|string $attributes
 * @return string
 */
function df_block_render_simple($attributes) {
	return df_block_render('core/template', '', $attributes);
}

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
 * @param bool $showCodeContext[optional]
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
	if ((Mage::getIsDeveloperMode() && (false !== $rethrow)) || (true === $rethrow)) {
		/**
		 * Чтобы кириллица отображалась в верной кодировке —
		 * пробуем отослать браузеру заголовок Content-Type.
		 *
		 * Обратите внимание, что такой подход не всегда корректен:
		 * ведь нашу исключительную ситуацию может поймать и обработать
		 * ядро Magento или какой-нибудь сторонний модуль, и они затем могут
		 * захотеть вернуть браузеру документ другого типа (не text/html).
		 * Однако, по-правильному они должны при этом сами установить свой Content-type/
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

/**
 * @param Mage_Core_Model_Abstract|string $model
 * @param int|string $id
 * @param string|null $field [optional]
 * @param bool $throwOnError [optional]
 * @return Mage_Core_Model_Abstract|null
 */
function df_load($model, $id, $field = null, $throwOnError = true) {
	/**
	 * Обратите внимание, что идентификатор необязательно является целым числом,
	 * потому что объект может загружаться по нестандартному ключу
	 * (с указанием этого ключа параметром $field).
	 * Так же, и первичный ключ может не быть чцелым числом (например, при загрузке валют).
	 */
	df_assert($id);
	if (!is_null($field)) {
		df_param_string($field, 2);
	}
	/** @var Mage_Core_Model_Abstract|null $result */
	$result = is_string($model) ? df_model($model) : $model;
	df_assert($result instanceof Mage_Core_Model_Abstract);
	$result->load($id, $field);
	if (!$result->getId()) {
		if (!$throwOnError) {
			$result = null;
		}
		else {
			df_error(
				'Система не нашла в базе данных объект класса «%s» с идентификатором «%d».'
				,get_class($result)
				,$id
			);
		}
	}
	if (!is_null($result)) {
		/** @var mixed $modelId */
		$modelId = is_null($field) ? $result->getId() : $result->getData($field);
		/**
		 * Обратите внимание, что мы намеренно используем !=, а не !==
		 */
		if ($id != $modelId) {
			if (!$throwOnError) {
				$result = null;
			}
			else {
				df_error(
					'При загрузке из базы данных объекта класса «%s» произошёл сбой: '
					.' идентификатор объекта должен быть равен «%s», а вместо этого равен «%s».'
					,get_class($result)
					,$id
					,$modelId
				);
			}
		}
	}
	return $result;
}

/** @return Df_Core_Helper_Mage */
function df_mage() {return Df_Core_Helper_Mage::s();}

/**
 * @param string $param1[optional]
 * @param string $param2[optional]
 * @return string|boolean
 */
function df_magento_version($param1 = null, $param2 = null) {
	return df()->version()->magentoVersion($param1, $param2);
}

/**
 * В качестве параметра $modelClass можно передавать:
 * 1) класс модели в стандартном формате
 * 2) класс модели в формате Magento
 * @param string $modelClass
 * @param array(string => mixed) $parameters [optional]
 * @return Mage_Core_Model_Abstract
 * @throws Exception
 */
function df_model($modelClass = '', $parameters = array()) {
	/**
	 * Удаление df_param_string
	 * ускорило загрузку главной страницы на эталонном тесте
	 * с 1.501 сек. до 1.480 сек.
	 */
	/** @var Mage_Core_Model_Abstract $result */
	$result = null;
	try {
		$result = Mage::getModel($modelClass, $parameters);
		if (!is_object($result)) {
			df_error('Не найден класс «%s»', $modelClass);
		}
		/**
		 * Обратите внимание, что Mage::getModel
		 * почему-то не устанавливает поле @see Varien_Object::_hasModelChanged в true.
		 * Мы же ранее устанавливали этот флаг в данной функции df_model,
		 * однако теперь это делаем более эффективным способом:
		 * в @see Df_Core_Model_Abstract::_construct().
		 * Обратите внимание, что у нас после недавнего рефакторинга (январь 2014 года)
		 * большинство моделей теперь содаётся через new, а не через df_model,
		 * поэтому установка флага в df_model теперь не только неэффективна, но и некорректна.
		 */
	}
	catch(Exception $e) {
		Mage::logException($e);
		/** @var array $bt */
		$bt = debug_backtrace();
		/** @var array $caller */
		$caller = df_a($bt, 1);
		/** @var string $className */
		$className = df_a($caller, 'class');
		/** @var string $methodName */
		$methodName = df_a($caller, 'function');
		/** @var string $methodNameWithClassName */
		$methodNameWithClassName = implode('::', array($className, $methodName));
		df_error(strtr(
			"%method%[%line%]\nНе могу создать модель класса «%modelClass%»."
			."\nСообщение системы: «%message%»"
			,array(
				'%method%' => $methodNameWithClassName
				,'%line%' => df_a(df_a($bt, 0), "line")
				,'%modelClass%' => $modelClass
				,'%message%' => rm_ets($e)
			)
		));
	}
	return $result;
}

/**
 * @param string $moduleName
 * @return bool
 */
function df_module_enabled($moduleName) {
	/** @var Df_Core_Model_Cache_Module $cacher */
	static $cacher;
	if (!isset($cacher)) {
		$cacher = Df_Core_Model_Cache_Module::s();
	}
	return $cacher->isEnabled($moduleName);
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
			Df_Qa_Message_Notification::P__NOTIFICATION => rm_sprintf($arguments)
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
		$message = rm_sprintf($arguments);
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
 * Если у Вас нет объекта класса Exception, то используйте @see df_notify()
 *
 * @param Exception|string $exception
 * @param string|null $additionalMessage [optional]
 * @return void
 */
function df_notify_exception($exception, $additionalMessage = null) {
	if (is_string($exception)) {
		$exception = new Df_Core_Exception_Client($exception);
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
		$message = rm_sprintf($arguments);
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
 * @param object|Varien_Object $entity
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function df_o($entity, $key, $default = null) {
	/**
	 * Раньше функция @see df_a была универсальной:
	 * она принимала в качестве аргумента $entity как массивы, так и объекты.
	 * В 99.9% случаев в качестве параметра передавался массив.
	 * Поэтому ради ускорения работы системы
	 * вынес обработку объектов в отдельную функцию @see df_o
	 */
	/** @var mixed $result */
	if (!is_object($entity)) {
		df_error('Попытка вызова df_o для переменной типа «%s».', gettype($entity));
	}
	if ($entity instanceof Varien_Object) {
		$result = $entity->getData($key);
		if (is_null($result)) {
			$result = $default;
		}
	}
	else {
		/**
		 * Например, stdClass.
		 * Используется, например, методом
		 * @see Df_Qiwi_Model_Action_Confirm::updateBill
		 */
		$result = isset($entity->{$key}) ? $entity->{$key} : $default;
	}
	return $result;
}

/** @return Df_Core_Helper_Output */
function df_output() {return Df_Core_Helper_Output::s();}

/**
 * Обратите внимание, что эта функция должна находиться именно в модуле Df_Core,
 * а не в модуле Df_Phpquery.
 * Перемещение этой функции в модуль Df_Phpquery
 * приведёт к сбою «Call to undefined function df_pq()»,
 * потому что перед использованием глобальных функций модуля Df_Phpquery
 * надо вызывать df_h()->phpquery()->lib(),
 * а метод df_h()->phpquery()->lib() вызывается именно внутри функции df_pq().
 * @param $arguments
 * @param $context[optional]
 * @return phpQueryObject
 */
function df_pq($arguments, $context = null) {
	/** @var bool $initialized */
	static $initialized = false;
	if (false === $initialized) {
		df_h()->phpquery()->lib();
		$initialized = true;
	}
	/** @var phpQueryObject|bool $result */
	$result = null;
	if (is_null($context) && is_string($arguments)) {
		$result = phpQuery::newDocument($arguments);
	}
	else {
		/** @var mixed[] $args */
		$args = func_get_args();
		$result = call_user_func_array(array('phpQuery', 'pq'), $args);
	}
	df_assert($result instanceof phpQueryObject);
	return $result;
}

/**
 * @param string $key
 * @param string $default[optional]
 * @return string
 */
function df_request($key, $default = null) {return df()->request()->getParam($key, $default);}

/** @return string */
function df_t() {
	/**
	 * Обратите внимание, что этот метод нельзя записать в одну строку,
	 * потому что функция func_get_args() не может быть параметром другой функции.
	 */
	/** @var mixed[] $fa */
	$fa = func_get_args();
	return
		Mage::app()->getTranslator()->translate(
			array_merge(
				array(new Mage_Core_Model_Translate_Expr(df_a($fa, 1), df_a($fa, 0)))
				,array_slice($fa, 2)
			)
		)
	;
}

/** @return Df_Core_Helper_Url */
function df_url() {return df_h()->core()->url();}

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
	 * Позволяет инициализировать те поля, которые отсутствуют в @see Varien_Object::getData(),
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
function rm_ceil($value) {return intval(ceil($value));}

/**
 * @param string $className
 * @return string
 */
function rm_class_mf($className) {
	return Df_Core_Model_Reflection::s()->getModelNameInMagentoFormat($className);
}

/**
 * @param string $className
 * @return string
 */
function rm_class_mf_r($className) {
	/** @var Df_Core_Model_Reflection $reflection */
	static $reflection;
	if (!isset($reflection)) {
		$reflection = df()->reflection();
	}
	return $reflection->getResourceNameInMagentoFormat($className);
}

/**
 * @param mixed $value
 * @return mixed
 */
function rm_empty_to_null($value) {return $value ? $value : null;}

/**
 * @param float|int $value
 * @return int
 */
function rm_floor($value) {return intval(floor($value));}

/**
 * @param bool $condition
 * @param mixed $resultOnTrue
 * @param mixed $resultOnFalse [optional]
 * @return mixed
 */
function rm_if($condition, $resultOnTrue, $resultOnFalse = null) {
	return $condition ? $resultOnTrue : $resultOnFalse;
}

/**
 * @param string $handle
 * @return bool
 */
function rm_handle_presents($handle) {
	/** @var bool[] $cache */
	static $handles;
	if (!isset($handles)) {
		$handles = array_flip(rm_handles());
	}
	/**
	 * @see array_flip() / @see isset() работает быстрее, чем @see in_array()
	 */
	return isset($handles[$handle]);
}

/** @return string[] */
function rm_handles() {return rm_layout()->getUpdate()->getHandles();}

/**
 * @see rm_sc()
 * @param string $resultClass
 * @param string $expectedClass
 * @param array(string => mixed) $params [optional]
 * @return Varien_Object|object
 */
function rm_ic($resultClass, $expectedClass, array $params = array()) {
	/** @var Varien_Object|object $result */
	$result = new $resultClass($params);
	df_assert($result instanceof $expectedClass);
	return $result;
}

/** @return Mage_Core_Model_Layout */
function rm_layout() {return Mage::getSingleton('core/layout');}

/**
 * @param Varien_Object|mixed[]|mixed $value
 * @return void
 */
function rm_log($value) {Mage::log(Df_Core_Model_Debug_Dumper::s()->dump($value));}

/**
 * @param Mage_Core_Controller_Response_Http $httpResponse
 * @param string $contentType
 * @return void
 */
function rm_response_content_type(Mage_Core_Controller_Response_Http $httpResponse, $contentType) {
	/**
	 * При установке заголовка HTTP «Content-Type»
	 * надёжнее всегда добавлять 3-й параметр: $replace = true,
	 * потому что заголовок «Content-Type» уже ранее был установлен методом
	 * @see Mage_Core_Model_App::getResponse()
	 */
	$httpResponse->setHeader('Content-Type', $contentType, $replace = true);
}

/**
 * 2015-03-23
 * @see rm_ic()
 * @param string $resultClass
 * @param string $expectedClass
 * @param array(string => mixed) $params [optional]
 * @return Varien_Object|object
 */
function rm_sc($resultClass, $expectedClass, array $params = array()) {
	/** @var array(string => object) $cache */
	static $cache;
	if (!isset($cache[$resultClass])) {
		$cache[$resultClass] = rm_ic($resultClass, $expectedClass, $params);
	}
	return $cache[$resultClass];
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
function rm_round($value) {return intval(round($value));}

/**
 * @param SimpleXMLElement $element
 * @param string $key
 * @param string|null $default [optional]
 * @return string
 */
function rm_simple_xml_a(SimpleXMLElement $element, $key, $default = null) {
	/** @var @mixed $result */
	$result = null;
	if (isset($element->$key)) {
		/** @var string[] $resultAsArray */
		$resultAsArray = $element->$key;
		if (is_null($resultAsArray)) {
			$result = $default;
		}
		else {
			$result = (string)$resultAsArray;
		}
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
	if (!isset($result)) {
		/** @var string $result */
		$result = (string)(Mage::getConfig()->getNode('df/version'));
		if (df()->version()->isItFree()) {
			$result .= '-free';
		}
		else {
			if (df()->version()->isItAdmin()) {
				$result .= '-admin';
			}
		}
	}
	return $result;
}

/** @return string */
function rm_version_full() {
	return rm_sprintf('%s (%s)', rm_version(), Mage::getVersion());
}