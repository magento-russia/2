<?php
// 2016-10-31
namespace Df\Core;
abstract class Request extends \Df_Core_Model {
	/**
	 * 2016-10-31
	 * @used-by getResponse()
	 * @used-by report()
	 * @return string
	 */
	abstract protected function apiName();

	/**
	 * Веб-сервисы служб доставки часто возвращают данные в формате,
	 * очень похожем на JSON, но требующем некоторых корректировок
	 * перед вызовом @see Zend_Json::decode()
	 * @used-by \Df\Core\Response::json()
	 * @param string $responseAsText
	 * @return string
	 */
	public function preprocessJson($responseAsText) {return $responseAsText;}

	/**
	 * @return string
	 */
	public function report() {
		/** @var string[] $parts */
		$parts = [
			'API: ' . $this->apiName()
			,'Адрес: ' . $this->zuri()->__toString()
		];
		/** @var array(string => string) $params */
		$params = $this->post() + $this->query();
		if ($params || $this->postRaw()) {
			$parts[]= 'Параметры:';
			if ($params) {
				$parts[]= df_print_params($params);
			}
			if ($this->postRaw()) {
				$parts[]= $this->postRaw();
			}
		}
		return(df_cc_n($parts));
	}

	/**
	 * @used-by \Df\Exline\Locator::_map()
	 * @return Response
	 */
	public function response() {
		if (!isset($this->_response)) {
			$this->_response = Response::i($this, $this->getResponseAsText());
			if (df_my_local()) {
				$this->_response->log();
			}
			$this->responseFailureDetect();
		}
		return $this->_response;
	}

	/**
	 * 2016-10-31
	 * @var Response
	 */
	private $_response;

	/**
	 * 2016-10-31
	 * @used-by getCache()
	 * @return string
	 */
	public static function cacheTypeS() {return __CLASS__;}

	/**
	 * Этот метод предназначен для перекрытия потомкими.
	 * @see Df_RussianPost_Model_Official_Request_International::adjust()
	 * @used-by client()
	 * @param \Zend_Http_Client $client
	 * @return void
	 */
	protected function adjust(\Zend_Http_Client $client) {}

	/**
	 * @used-by \Df\Shipping\Request::deliveryTime()
	 * @used-by \Df\Shipping\Request::deliveryTimeMax()
	 * @used-by \Df\Shipping\Request::deliveryTimeMin()
	 * @used-by \Df\Shipping\Request::rate()
	 * @return mixed
	 * @throws Exception
	 */
	protected function call() {return dfc($this, function($method) {
		/** @var mixed $result */
		$result = $this->try_([$this, "_{$method}"]);
		/** @var string $filter */
		$filter = "_{$method}Filter";
		return !method_exists($this, $filter) ? $result : $this->try_([$this, $filter], $result);
	}, [df_caller_f()]);}

	/**
	 * Я думаю, будет нормальным обновлять кэш раз в месяц.
	 * Уж пожизненно его точно не стоит хранить, ибо тарифы служб доставки меняются.
	 * @return \Df_Core_Model_Cache
	 */
	protected function getCache() {return dfc($this, function() {return
		\Df_Core_Model_Cache::i(static::cacheTypeS(), 30 * 86400)
	;});}

	/** @return string */
	protected function getErrorMessage() {return '';}

	/** @return array */
	protected function getRequestConfuguration() {return [];}

	/** @return string */
	protected function getResponseAsTextInternal() {
		/** @var string $result */
		$result = $this->getResponse()->getBody();
		if ($this->needConvertResponseFrom1251ToUtf8()) {
			$result = str_replace('charset=windows-1251', 'charset=utf-8', df_1251_from($result));
		}
		return $result;
	}

	/**
	 * @used-by client()
	 * @return array(string => string)
	 */
	protected function headers() {return [];}

	/** @return string */
	protected function method() {return $this->cfg(self::P__METHOD, \Zend_Http_Client::GET);}

	/** @return bool */
	protected function needCacheResponse() {return $this->getCache()->isEnabled();}

	/** @return bool */
	protected function needConvertResponseFrom1251ToUtf8() {return false;}

	/** @return bool */
	protected function needLogNonExceptionErrors() {return true;}

	/**
	 * 	Некоторые калькуляторы допускают несколько одноимённых опций.
	 *  http_build_query кодирует их как a[0]=1&a[1]=2&a[2]=3
	 *  Если калькулятору нужно получить a=1&a=2&a=3,
	 *  то перекройте этот метод и верните true.
	 * @return bool
	 */
	protected function needPostKeysWithSameName() {return false;}

	/** @return array(string => string) */
	protected function post() {return $this->cfg(self::P__POST, array());}

	/** @return string */
	protected function postRaw() {return '';}

	/** @return array(string => string) */
	protected function query() {return $this->cfg(self::P__QUERY, []);}

	/**
	 * @return void
	 * @throws Exception
	 */
	protected function responseFailureDetect() {}

	/**
	 * 2016-10-29
	 * @used-by zuri()
	 * @return string|null
	 */
	protected function suffix() {return $this[self::P__SUFFIX];}

	/**
	 * 2016-10-31
	 * @used-by call()
	 * @param callable $f
	 * @param ... $args [optional]
	 * @return mixed
	 * @throws \Exception
	 */
	protected function try_(callable $f, ...$args) {
		try {return call_user_func_array($f, $args);}
		catch (\Exception $e) {
			// В случае сбоя на стороне службы доставки расчёта тарифа
			// (например, служба Деловые Линии может выдавать ответ:
			// «Сервис временно недоступен. Попробуйте посчитать стоимость доставки чуть позже.»)
			// надо удалить кэш,
			// чтобы при следующем оформлении заказа модуль сделал запрос тарифа заново.
			$this->removeCache();
			df_context('Адрес запроса', $this->zuri()->__toString(), 1);
			/** @var array(string => string) $params */
			$params = $this->post() + $this->query();
			if ($params) {
				df_context('Параметры запроса', df_print_params($params), 2);
			}
			if ($this->postRaw()) {
				df_context('Тело запроса', $this->postRaw(), 3);
			}
			throw $e;
		}
	}

	/**
	 * Ключ кэширования не должен содержать русские буквы,
	 * потому что когда кэш хранится в файлах, то русские буквы будут заменены на символ «_»,
	 * и имя файла будет выглядеть как «mage---b26_DF_LOCALIZATION_MODEL_MORPHER________».
	 * Чтобы избавиться от русских букв при сохранении уникальности ключа, испольузем функцию md5.
	 *
	 * $this->post() может вернуть многомерный массив
	 * (например, так происходит в модуле Df_Pec).
	 * Поэтому мы не используем "в лоб" array_merge, а используем http_build_query.
	 *
	 * @return string
	 */
	private function cacheKey() {return dfc($this, function() {return
		dfa_hashm(array_merge(
			[get_class($this), $this->zuri()->__toString()]
			,!$this->isItPost() ? [] : [$this->postRaw() ?: $this->post()]
		));
	});}

	/**
	 * Используем класс Zend_Http_Client, а не Varien_Http_Client,
	 * потому что применение Varien_Http_Client зачастую приводит к сбою:
	 * Error parsing body - doesn't seem to be a chunked message
	 * @return \Zend_Http_Client
	 */
	private function client() {return dfc($this, function() {
		/** @var \Zend_Http_Client $result */
		$result = new \Zend_Http_Client();
		$result->setHeaders($this->headers());
		$result->setConfig($this->getRequestConfuguration() + ['timeout' => 10]);
		$this->adjust($result);
		return $result;
	});}

	/** @return \Zend_Http_Response */
	private function getResponse() {return dfc($this, function() {
		$this->client()->setUri($this->zuri());
		if ($this->isItPost()) {
			if ($this->postRaw()) {
				$this->client()->setRawData($this->postRaw());
			}
			else {
				if (!$this->needPostKeysWithSameName()) {
					$this->client()->setParameterPost($this->post());
				}
				else {
					$this->client()->setRawData(
						/**
						 * Некоторые калькуляторы допускают несколько одноимённых опций.
						 * http_build_query кодирует их как a[0]=1&a[1]=2&a[2]=3
						 * Чтобы убрать квадратные скобки, используем регулярное выражение
						 * http://www.php.net/manual/en/function.http-build-query.php#78603
						 */
						preg_replace(
							'#%5B(?:[0-9]|[1-9][0-9]+)%5D=#u'
							,'='
							,http_build_query($this->post(), '', '&')
						)
					);
				}
			}
		}
		/** @var \Zend_Http_Response $result */
		$result = $this->try_(function() {return $this->client()->request($this->method());});
		/**
		 * Обратите внимание,
		 * что обычное @see Zend_Uri::__toString() здесь для сравнения использовать нельзя,
		 * потому что Zend Framework свежих версий Magento CE (заметил в Magento CE 1.9.0.1)
		 * зачем-то добавляет ко второму веб-адресу $this->client()->getUri()
		 * порт по-умолчанию (80), даже если в первом веб-адресе ($this->zuri())
		 * порт отсутствует.
		 */
		if (!\Df\Zf\UriComparator::c($this->zuri(), $this->client()->getUri())) {
			/**
			 * Сервер службы доставки перенаправил нас на новый адрес.
			 * С большой вероятностью, это означает, что изменился программный интерфейс службы доставки
			 * (или изменился веб-интерфейс калькулятора службы доставки,
			 * если модуль работает посредством парсинга веб-интерфейса калькулятора).
			 * Извещаем об этом разработчика.
			 */
			df_notify_me(
				'API «%s» перенаправил запрос «%s» с адреса «%s» на неожиданный адрес «%s».'
				, $this->apiName()
				, get_class($this)
				, $this->zuri()->__toString()
				, $this->client()->getUri()->__toString()
			);
		}
		return $result;
	});}

	/**
	 * @return string
	 * @throws \Df\Core\Exception\Request
	 */
	private function getResponseAsText() {return dfc($this, function() {
		/** @var string $result */
		$result = false;
		if ($this->needCacheResponse()) {
			$result = $this->getCache()->loadData($this->cacheKey());
		}
		if (false === $result) {
			try {
				$result = $this->getResponseAsTextInternal();
			}
			catch (Exception $e) {
				$this->removeCache();
				throw new \Df\Core\Exception\Request($e, $this);
			}
			if ($this->needCacheResponse()) {
				$this->getCache()->saveData($this->cacheKey(), $result);
			}
		}
		return $result;
	});}

	/** @return bool */
	private function isItPost() {return \Zend_Http_Client::POST === $this->method();}

	/** @return void */
	private function removeCache() {
		if ($this->needCacheResponse()) {
			$this->getCache()->removeData($this->cacheKey());
		}
	}

	/**
	 * 2016-10-29
	 * @used-by zuri()
	 * @return string
	 */
	abstract protected function uri();

	/** @return \Zend_Uri_Http */
	private function zuri() {return dfc($this, function() {
		/** @var \Zend_Uri_Http $result */
		$result = \Zend_Uri::factory($this->uri());
		$result->setPath($result->getPath() . $this->suffix());
		$result->setQuery(df_clean($this->query()));
		return $result;
	});}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__METHOD, DF_V_STRING, false)
			->_prop(self::P__POST, DF_V_ARRAY, false)
			->_prop(self::P__QUERY, DF_V_ARRAY, false)
			->_prop(self::P__SUFFIX, DF_V_STRING, false)
		;
	}

	const P__METHOD = 'request_method';
	const P__POST = 'post_params';
	const P__QUERY = 'query_params';
	const P__SUFFIX = 'suffix';
}