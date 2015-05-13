<?php
class Df_Shipping_Model_Request extends Df_Core_Model_Abstract {
	/**
	 * Веб-сервисы служб доставки часто возвращают данные в формате,
	 * очень похожем на JSON, но требующем некоторых корректировок
	 * перед вызовом Zend_Json::decode.
	 * Метод публичен, потому что его использует класс @see Df_Shipping_Model_Response
	 * @param string $responseAsText
	 * @return string
	 */
	public function preprocessJson($responseAsText) {return $responseAsText;}

	/** @return Df_Shipping_Model_Response */
	public function response() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Shipping_Model_Response::i($this, $this->getResponseAsText());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by getHttpClient()
	 * @param Zend_Http_Client $httpClient
	 * @return void
	 */
	protected function adjustHttpClient(Zend_Http_Client $httpClient) {}

	/** @return Df_Core_Model_Cache */
	protected function getCache() {
		if (!isset($this->{__METHOD__})) {
			// Я думаю, будет нормальным обновлять кэш раз в месяц.
			// Уж пожизненно его точно не стоит хранить, ибо тарифы служб доставки меняются.
			$this->{__METHOD__} = Df_Core_Model_Cache::i(self::CACHE_TYPE, 30 * 86400);
		}
		return $this->{__METHOD__};
	}

	/** @return string[] */
	protected function getCacheKeyParams() {
		/** @var string[] $result */
		$result = array(get_class($this), md5($this->getUri()->__toString()));
		if ($this->isItPost()) {
			/**
			 * Обратите внимание, что $this->getPostParameters()
			 * может вернуть многомерный массив (например, так происходит в модуле Df_Pec).
			 * Поэтому мы не используем "в лоб" array_merge, а используем http_build_query.
			 */
			$result['post'] =
				$this->getPostRawData()
				? $this->getPostRawData()
				: http_build_query($this->getPostParameters())
			;
		}
		return $result;
	}

	/** @return Df_Shipping_Model_Carrier */
	protected function getCarrier() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $className */
			$className = Df_Core_Model_ClassManager::s()->getResourceClass($this, 'Model_Carrier');
			df_assert_ne('Df_Shipping_Model_Carrier', $className);
			$this->{__METHOD__} = new $className(array(
				Df_Shipping_Model_Carrier::P__STORE => Mage::app()->getStore()
			));
			df_assert($this->{__METHOD__} instanceof Df_Shipping_Model_Carrier);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	protected function getErrorMessage() {return '';}

	/** @return array(string => string) */
	protected function getHeaders() {return $this->cfg(self::P__HEADERS, array());}

	/** @return Zend_Http_Client */
	protected function getHttpClient() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * Обратите внимание,
			 * что мы используем класс Zend_Http_Client, а не Varien_Http_Client,
			 * потому что применение Varien_Http_Client зачастую приводит к сбою:
			 * Error parsing body - doesn't seem to be a chunked message
			 * @var Zend_Http_Client $httpClient
			 */
			$this->{__METHOD__} = new Zend_Http_Client();
			$this->{__METHOD__}
				->setHeaders($this->getHeaders())
				->setConfig(
					array_merge(
						array('timeout' => 10)
						,$this->getRequestConfuguration()
					)
				)
			;
			/** @var Zend_Http_CookieJar|null $cookieJar */
			$cookieJar = $this->cfg(self::P__COOKIE_JAR);
			if ($cookieJar) {
				$this->{__METHOD__}->setCookieJar($cookieJar);
			}
			$this->adjustHttpClient($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => string) */
	protected function getPostParameters() {return $this->cfg(self::P__POST_PARAMS, array());}

	/** @return string */
	protected function getPostRawData() {return '';}

	/** @return string|array(string => string) */
	protected function getQuery() {return df_clean($this->getQueryParams());}

	/** @return string */
	protected function getQueryHost() {return $this->cfg(self::P__QUERY_HOST, '');}

	/** @return array(string => string) */
	protected function getQueryParams() {return $this->cfg(self::P__QUERY_PARAMS, array());}

	/** @return string */
	protected function getQueryPath() {return $this->cfg(self::P__QUERY_PATH, '');}

	/** @return int|null */
	protected function getQueryPort() {return null;}

	/** @return array */
	protected function getRequestConfuguration() {return array();}

	/** @return string */
	protected function getRequestMethod() {
		return $this->cfg(self::P__REQUEST_METHOD, Zend_Http_Client::GET);
	}

	/** @return Zend_Uri_Http */
	protected function getUri() {
		if (!isset($this->{__METHOD__})) {
			/** @var Zend_Uri_Http $result */
			$result = Zend_Uri::factory('http');
			$result->setHost($this->getQueryHost());
			if ($this->getQueryPort()) {
				$result->setPort($this->getQueryPort());
			}
			$result->setPath($this->getQueryPath());
			$result->setQuery($this->getQuery());
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Shipping_Model_Request */
	protected function logRequestParameters() {
		df()->debug()->report(
			rm_sprintf('%s-{ordering}.log', Df_Core_Model_ClassManager::s()->getFeatureCode($this))
			,rm_print_params($this->getQueryParams())
		);
		return $this;
	}

	/** @return Df_Shipping_Model_Request */
	protected function logResponseAsHtml() {
		df()->debug()->report(
			rm_sprintf('%s-{ordering}.html', Df_Core_Model_ClassManager::s()->getFeatureCode($this))
			, $this->getResponseAsText()
		);
		return $this;
	}

	/** @return Df_Shipping_Model_Request */
	protected function logResponseAsJson() {
		/** @var mixed[] $responseAsArray */
		$responseAsArray = null;
		try {
			$responseAsArray = $this->response()->json();
		}
		catch (Exception $e) {}
		df()->debug()->report(
			rm_sprintf('%s-{ordering}.json', Df_Core_Model_ClassManager::s()->getFeatureCode($this))
			, is_null($responseAsArray)
			? $this->getResponseAsText()
			: rm_print_params($responseAsArray)
		);
		return $this;
	}

	/** @return string */
	protected function getResponseAsTextInternal() {
		/** @var string $result */
		$result = $this->getHttpResponse()->getBody();
		if ($this->needConvertResponseFrom1251ToUtf8()) {
			$result =
				str_replace(
					'charset=windows-1251', 'charset=utf-8'
					, df_text()->convertWindows1251ToUtf8($result)
				)
			;
		}
		return $result;
	}

	/** @return Df_Shipping_Model_Request */
	protected function logResponseAsXml() {
		df()->debug()->report(
			rm_sprintf('%s-{ordering}.xml', Df_Core_Model_ClassManager::s()->getFeatureCode($this))
			,$this->getResponseAsText()
		);
		return $this;
	}

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
	
	/**
	 * @return Df_Shipping_Model_Request
	 * @throws Exception
	 */
	protected function responseFailureDetect() {
		if (false === $this->_responseFailureChecked) {
			$this->responseFailureDetectInternal();
			$this->_responseFailureChecked = true;
		}
		return $this;
	}
	/** @var bool */
	private $_responseFailureChecked = false;

	/**
	 * @return Df_Shipping_Model_Request
	 * @throws Exception
	 */
	protected function responseFailureDetectInternal() {return $this;}

	/**
	 * @param Exception|string $message
	 * @return Df_Shipping_Model_Request
	 */
	protected function responseFailureHandle($message) {
		// В случае сбоя на стороне службы доставки расчёта тарифа
		// (например, служба Деловые Линии может выдавать ответ:
		// «Сервис временно недоступен. Попробуйте посчитать стоимость доставки чуть позже.»)
		// надо удалить кэш, чтобы при следующем оформлении заказа модуль сделал запрос тарифа заново.
		if ($this->needCacheResponse()) {
			$this->getCache()->removeData($this->getCacheKey_Shipping());
		}
		/** @var mixed[] $arguments */
		$arguments = func_get_args();
		if (1 < count($arguments)) {
			$message = rm_sprintf($arguments);
		}
		$this->logRequest($message);
		if ($message instanceof Exception) {
			/** @var Exception $e */
			$e = $message;
			$message = rm_ets($e);
			if (!($e instanceof Df_Core_Exception_Client)) {
				df_notify_exception($e);
				$message = df_mage()->shippingHelper()->__(Df_Shipping_Model_Carrier::T_INTERNAL_ERROR);
			}
			/**
			 * Вообще говоря, причиной сбоя self::MESSAGE__ERROR_PARSING_BODY
			 * был некорректно работающий класс Varien_Http_Client.
			 * После замены его на Varien_Http_Client побобный сбой вроде не возникает.
			 */
			if (self::MESSAGE__ERROR_PARSING_BODY === $message) {
				$message = 'Служба доставки не смогла рассчитать тариф';
			}
		}
		df_error($message ? $message : self::T__ERROR_MESSAGE__DEFAULT);
		return $this;
	}

	/**
	 * @param Exception|string |null$message [optional]
	 * @return Df_Shipping_Model_Request
	 */
	protected function logRequest($message = null) {
		/** @var bool $isException */
		$isException = ($message instanceof Exception);
		if ($isException) {
			$message = rm_ets($message);
		}
		if ($isException || $this->needLogNonExceptionErrors()) {
			/** @var array $logRecordParts */
			$logRecordParts = array(self::T__ERROR_MESSAGE__DEFAULT);
			$logRecordParts[]= rm_sprintf('Модуль: %s', $this->getCarrier()->getTitle());
			if ($message) {
				$logRecordParts[]= $message;
			}
			$logRecordParts[]= rm_sprintf('Адрес: %s', $this->getUri()->__toString());
			$logRecordParts[]=
				rm_print_params(
					$this->isItPost()
					? ($this->getPostRawData() ? $this->getPostRawData() : $this->getPostParameters())
					: $this->getUri()->getQueryAsArray()
				)
			;
			df_h()->shipping()->log(implode("\n", $logRecordParts), $сaller = $this);
		}
		return $this;
	}

	/** @return string */
	private function getCacheKey_Shipping() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * Обратите внимание, что ключ кэширования не должен содержать русские буквы,
			 * потому что когда кэш хранится в файлах, то русские буквы будут заменены на символ «_»,
			 * и имя файла будет выглядеть как «mage---b26_DF_LOCALIZATION_MODEL_MORPHER________».
			 * Чтобы избавиться от русских букв при сохранении уникальности ключа, испольузем функцию md5.
			 * @var string $cacheKey
			 */
			$this->{__METHOD__} = md5(implode('--', $this->getCacheKeyParams()));
		}
		return $this->{__METHOD__};
	}
	
	/** @return Zend_Http_Response */
	private function getHttpResponse() {
		if (!isset($this->{__METHOD__})) {
			$this->getHttpClient()->setUri($this->getUri());
			if ($this->isItPost()) {
				if ($this->getPostRawData()) {
					$this->getHttpClient()->setRawData($this->getPostRawData());
				}
				else {
					if (!$this->needPostKeysWithSameName()) {
						$this->getHttpClient()->setParameterPost($this->getPostParameters());
					}
					else {
						$this->getHttpClient()->setRawData(
							/**
							 * Некоторые калькуляторы допускают несколько одноимённых опций.
							 * http_build_query кодирует их как a[0]=1&a[1]=2&a[2]=3
							 * Чтобы убрать квадратные скобки, используем регулярное выражение
							 * @link http://www.php.net/manual/en/function.http-build-query.php#78603
							 */
							preg_replace(
								'#%5B(?:[0-9]|[1-9][0-9]+)%5D=#u'
								,'='
								,http_build_query(
									$this->getPostParameters()
									,''
									,'&'
								)
							)
						);
					}
				}
			}
			$this->{__METHOD__} = $this->getHttpClient()->request($this->getRequestMethod());
			/**
			 * Обратите внимание,
			 * что обычное @see Zend_Uri::__toString() здесь для сравнения использовать нельзя,
			 * потому что Zend Framework свежих версий Magento CE (заметил в Magento CE 1.9.0.1)
			 * зачем-то добавляет ко второму веб-адресу $this->getHttpClient()->getUri()
			 * порт по-умолчанию (80), даже если в первом веб-адресе ($this->getUri())
			 * порт отсутствует.
			 */
			if (!self::uriAreEqual($this->getUri(), $this->getHttpClient()->getUri())) {
				/**
				 * Сервер службы доставки перенаправил нас на новый адрес.
				 * С большой вероятностью, это означает, что изменился программный интерфейс службы доставки
				 * (или изменился веб-интерфейс калькулятора службы доставки,
				 * если модуль работает посредством парсинга веб-интерфейса калькулятора).
				 * Извещаем об этом разработчика.
				 */
				df_notify_me(
					'Сервер службы доставки «%s»'
					. ' перенаправил запрос «%s» с адреса «%s» на неожиданный адрес «%s».'
					, $this->getCarrier()->getTitle()
					, get_class($this)
					, $this->getUri()->__toString()
					, $this->getHttpClient()->getUri()->__toString()
				);
			}
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getResponseAsText() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = false;
			if ($this->needCacheResponse()) {
				$result = $this->getCache()->loadData($this->getCacheKey_Shipping());
			}
			if (false === $result) {
				try {
					$result = $this->getResponseAsTextInternal();
				}
				catch(Exception $e) {
					$this->responseFailureHandle($e);
				}
				if ($this->needCacheResponse()) {
					$this->getCache()->saveData($this->getCacheKey_Shipping(), $result);
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function isItPost() {return Zend_Http_Client::POST === $this->getRequestMethod();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__COOKIE_JAR, 'Zend_Http_CookieJar', false)
			->_prop(self::P__HEADERS, self::V_ARRAY, false)
			->_prop(self::P__POST_PARAMS, self::V_ARRAY, false)
			->_prop(self::P__QUERY_HOST, self::V_STRING, false)
			->_prop(self::P__QUERY_PARAMS, self::V_ARRAY, false)
			->_prop(self::P__QUERY_PATH, self::V_STRING, false)
			->_prop(self::P__REQUEST_METHOD, self::V_STRING, false)
		;
	}
	const _CLASS = __CLASS__;
	const CACHE_TYPE = 'rm_shipping';
	const MESSAGE__ERROR_PARSING_BODY =
		'Error parsing body - doesn\'t seem to be a chunked message'
	;
	const NO_INTERNET = false;
	const P__COOKIE_JAR = 'cookie_jar';
	const P__HEADERS = 'headers';
	const P__POST_PARAMS = 'post_params';
	const P__QUERY_HOST = 'query_host';
	const P__QUERY_PARAMS = 'query_params';
	const P__QUERY_PATH = 'query_path';
	const P__REQUEST_METHOD = 'request_method';
	const T__ERROR_MESSAGE__DEFAULT =
		'Обращение к программному интерфейсу службы доставки привело к сбою.'
	;

	/**
	 * @used-by uriAreEqual()
	 * @param string $host
	 * @return string
	 */
	private static function uriAdjustHost($host) {return df_trim_text_left($host, 'www.');}

	/**
	 * @used-by uriAreEqual()
	 * @param int|string|bool $port
	 * @return string
	 */
	private static function uriAdjustPort($port) {return (string)('80' === strval($port) ? '' : $port);}

	/**
	 * Обратите внимание,
	 * что обычное @see Zend_Uri::__toString() здесь для сравнения использовать нельзя,
	 * потому что Zend Framework свежих версий Magento CE (заметил в Magento CE 1.9.0.1)
	 * зачем-то добавляет ко второму веб-адресу $this->getHttpClient()->getUri()
	 * порт по-умолчанию (80), даже если в первом веб-адресе ($this->getUri())
	 * порт отсутствует.
	 *
	 * 2015-03-20
	 * Более того, некоторые службы доставки со временем меняют своё решение
	 * относительно использования «.www» в домене, и тогда мы получаем мусорные предупреждения
	 * о неравенстве веб-адресов типа:
			«http://kazpost.kz/calc/cost.php?from=4&obcen=1&obcentenge=35245&to=11&v=1&w=1»
			«http://www.kazpost.kz:80/calc/cost.php?from=4&obcen=1&obcentenge=35245&to=11&v=1&w=1».
	 * @used-by getHttpResponse()
	 * @param Zend_Uri_Http $uri1
	 * @param Zend_Uri_Http $uri2
	 * @return bool
	 */
	private static function uriAreEqual(Zend_Uri_Http $uri1, Zend_Uri_Http $uri2) {
		return
			$uri1->getScheme() === $uri2->getScheme()
			&& self::uriAdjustHost($uri1->getHost()) === self::uriAdjustHost($uri2->getHost())
			&& self::uriAdjustPort($uri1->getPort()) === self::uriAdjustPort($uri2->getPort())
			/**
			 * 2015-03-23
			 * Бывают в разном регистре.
			 */
			&& df_strings_are_equal_ci($uri1->getQuery(), $uri2->getQuery())
		;
	}
}