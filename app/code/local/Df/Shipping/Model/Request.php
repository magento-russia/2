<?php
class Df_Shipping_Model_Request extends Df_Core_Model {
	/**
	 * @used-by \Df\Shipping\Exception\Request::carrier()
	 * @return Df_Shipping_Carrier
	 */
	public function getCarrier() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $className */
			$className = df_con($this, 'Carrier');
			$this->{__METHOD__} = new $className;
			df_assert($this->{__METHOD__} instanceof Df_Shipping_Carrier);
			$this->{__METHOD__}->setStore(df_store());
		}
		return $this->{__METHOD__};
	}

	/**
	 * Не все запросы к серверу предназначены для получения срока доставки,
	 * однако иметь метод @see getDeliveryTime() в базовом классе выгодно:
	 * смотрите комментарий к методу @see getRate()
	 * @uses _filterDeliveryTime()
	 * @uses _getDeliveryTime()
	 * @return int
	 */
	public function getDeliveryTime() {return $this->call(__FUNCTION__);}

	/**
	 * Не все запросы к серверу предназначены для получения срока доставки,
	 * однако иметь метод @see getDeliveryTimeMax() в базовом классе выгодно:
	 * смотрите комментарий к методу @see getRate()
	 * @uses _filterDeliveryTimeMax()
	 * @uses _getDeliveryTimeMax()
	 * @return int
	 */
	public function getDeliveryTimeMax() {return $this->call(__FUNCTION__);}

	/**
	 * Не все запросы к серверу предназначены для получения срока доставки,
	 * однако иметь метод @see getDeliveryTimeMin() в базовом классе выгодно:
	 * смотрите комментарий к методу @see getRate()
	 * @uses _filterDeliveryTimeMin()
	 * @uses _getDeliveryTimeMin()
	 * @return int
	 */
	public function getDeliveryTimeMin() {return $this->call(__FUNCTION__);}

	/**
	 * 2015-02-20
	 * Не все запросы к серверу предназначены для получения тарифа.
	 * Например, некоторые запросы предназначекны для получения перечня пунктов доставки.
	 * Однако, иметь метод @see getRate() в базовом классе нам очень удобно:
	 * это позволяет не дублировать данную функциональность в тех классах-потомках, где она требуется.
	 * Другими словами, у нас ситуация: метод @see getRate() нужен примерно половине классов-потомков,
	 * однако мы не можем вынести метод @see getRate() в общий подкласс-родитель той половины
	 * классов потомков класса @see Df_Shipping_Model_Request, которым требуется метод @see @see getRate(),
	 * потому что у этих классов уже есть своя иерархия (иерархия по службе доставки: у API каждой службы
	 * доставки ведь своя специфика и своя общая функциональность для всех потомков).
	 * @uses _filterRate()
	 * @uses _getRate()
	 * @return float|int
	 */
	public function getRate() {return $this->call(__FUNCTION__);}

	/**
	 * Веб-сервисы служб доставки часто возвращают данные в формате,
	 * очень похожем на JSON, но требующем некоторых корректировок
	 * перед вызовом @see Zend_Json::decode()
	 * @used-by Df_Shipping_Model_Response::json()
	 * @param string $responseAsText
	 * @return string
	 */
	public function preprocessJson($responseAsText) {return $responseAsText;}

	/**
	 * @return string
	 */
	public function report() {
		/** @var string[] $parts */
		$parts = array(
			'Модуль: ' . $this->getCarrier()->getTitle()
			,'Адрес: ' . $this->getUri()->__toString()
		);
		/** @var array(string => string) $params */
		$params = $this->getPostParameters() + $this->getQueryParams();
		if ($params || $this->getPostRawData()) {
			$parts[]= 'Параметры:';
			if ($params) {
				$parts[]= df_print_params($params);
			}
			if ($this->getPostRawData()) {
				$parts[]= $this->getPostRawData();
			}
		}
		return(df_cc_n($parts));
	}

	/**
	 * @used-by Df_Exline_Locator::_map()
	 * @return Df_Shipping_Model_Response
	 */
	public function response() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Shipping_Model_Response::i($this, $this->getResponseAsText());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by Df_Shipping_Model_Request::getDeliveryTime()
	 * @param string|int $value
	 * @return int
	 */
	protected function _filterDeliveryTime($value) {return df_nat($value);}

	/**
	 * @used-by Df_Shipping_Model_Request::getDeliveryTimeMax()
	 * @param string|int $value
	 * @return int
	 */
	protected function _filterDeliveryTimeMax($value) {return $this->_filterDeliveryTime($value);}

	/**
	 * @used-by Df_Shipping_Model_Request::getDeliveryTimeMin()
	 * @param string|int $value
	 * @return int
	 */
	protected function _filterDeliveryTimeMin($value) {return $this->_filterDeliveryTime($value);}

	/**
	 * @used-by Df_Shipping_Model_Request::getRate()
	 * @param float|int|string $value
	 * @return float
	 */
	protected function _filterRate($value) {return df_float_positive($value);}

	/**
	 * Этот метод предназначен для перекрытия потомками.
	 * @used-by Df_Shipping_Model_Request::getDeliveryTime()
	 * @return int|string
	 */
	protected function _getDeliveryTime() {df_abstract($this); return 0;}

	/**
	 * Этот метод предназначен для перекрытия потомками.
	 * @used-by Df_Shipping_Model_Request::getRate()
	 * @return float|int|string
	 */
	protected function _getRate() {df_abstract($this); return 0;}

	/**
	 * Этот метод предназначен для перекрытия потомкими.
	 * @see Df_RussianPost_Model_Official_Request_International::adjustHttpClient()
	 * @used-by getHttpClient()
	 * @param Zend_Http_Client $httpClient
	 * @return void
	 */
	protected function adjustHttpClient(Zend_Http_Client $httpClient) {}

	/**
	 * @used-by getDeliveryTime()
	 * @used-by getDeliveryTimeMax()
	 * @used-by getDeliveryTimeMin()
	 * @used-by getRate()
	 * @used-by Df_Cdek_Model_Request_Rate::getServiceId()
	 * @param string $method
	 * @return mixed
	 * @throws Exception
	 */
	protected function call($method) {
		if (!isset($this->{__METHOD__}[$method])) {
			try {
				if (false === $this->_responseFailureChecked) {
					$this->responseFailureDetect();
					$this->_responseFailureChecked = true;
				}
				/**
				 * Вызываем внутренний метод для извлечения данных из ответа сервера.
				 * Например, для метода @see getRate() будет вызван метод @see _getRate().
				 */
				/** @var mixed $result */
				$result = call_user_func([$this, '_' . $method]);
				/**
				 * Выполняем фильтрацию и проверку результата.
				 * Например, для метода @see getRate() будет вызван метод @see _filterRate().
				 */
				/** @var string $filter */
				$filter = '_filter' . df_trim_text_left($method, 'get');
				if (method_exists($this, $filter)) {
					$result = call_user_func([$this, $filter], $result);
				}
				$this->{__METHOD__}[$method] = $result;
			}
			catch (Exception $e) {
				// В случае сбоя на стороне службы доставки расчёта тарифа
				// (например, служба Деловые Линии может выдавать ответ:
				// «Сервис временно недоступен. Попробуйте посчитать стоимость доставки чуть позже.»)
				// надо удалить кэш,
				// чтобы при следующем оформлении заказа модуль сделал запрос тарифа заново.
				$this->removeCache();
				throw $e;
			}
		}
		return $this->{__METHOD__}[$method];
	}

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

	/**
	 * 2015-02-21
	 * Этот метод предназначен для перекрытия потомками.
	 * @used-by Df_Shipping_Model_Request::getDeliveryTime()
	 * @return int
	 */
	protected function getDeliveryTimeInternal() {df_abstract($this); return 0;}

	/** @return string */
	protected function getErrorMessage() {return '';}

	/** @return array(string => string) */
	protected function getHeaders() {return array(
		'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:12.0) Gecko/20100101 Firefox/12.0'
	);}

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
				->setConfig($this->getRequestConfuguration() + array('timeout' => 10))
			;
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
	 * 2016-10-24
	 * @used-by Df_Shipping_Model_Request::getUri()
	 * @return string
	 */
	protected function scheme() {return 'http';}

	/** @return Zend_Uri_Http */
	protected function getUri() {
		if (!isset($this->{__METHOD__})) {
			/** @var Zend_Uri_Http $result */
			$result = Zend_Uri::factory($this->scheme());
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
	 * @return void
	 * @throws Exception
	 */
	protected function responseFailureDetect() {}

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
	private function getResponse() {
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
							 * http://www.php.net/manual/en/function.http-build-query.php#78603
							 */
							preg_replace(
								'#%5B(?:[0-9]|[1-9][0-9]+)%5D=#u'
								,'='
								,http_build_query($this->getPostParameters(), '', '&')
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
			if (!Df\Zf\UriComparator::c($this->getUri(), $this->getHttpClient()->getUri())) {
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

	/**
	 * @return string
	 * @throws \Df\Shipping\Exception\NoResponse
	 */
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
				catch (Exception $e) {
					$this->removeCache();
					throw new \Df\Shipping\Exception\NoResponse($e, $this);
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

	/** @return void */
	private function removeCache() {
		if ($this->needCacheResponse()) {
			$this->getCache()->removeData($this->getCacheKey_Shipping());
		}
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__POST_PARAMS, DF_V_ARRAY, false)
			->_prop(self::P__QUERY_HOST, DF_V_STRING, false)
			->_prop(self::P__QUERY_PARAMS, DF_V_ARRAY, false)
			->_prop(self::P__QUERY_PATH, DF_V_STRING, false)
			->_prop(self::P__REQUEST_METHOD, DF_V_STRING, false)
		;
	}
	/** @var bool */
	private $_responseFailureChecked = false;

	/** @used-by Df_Shipping_Model_Response::_construct() */
	const _C = __CLASS__;
	const CACHE_TYPE = 'rm_shipping';
	const MESSAGE__ERROR_PARSING_BODY = 'Error parsing body - doesn\'t seem to be a chunked message';
	const P__POST_PARAMS = 'post_params';
	const P__QUERY_HOST = 'query_host';
	const P__QUERY_PARAMS = 'query_params';
	const P__QUERY_PATH = 'query_path';
	const P__REQUEST_METHOD = 'request_method';
	const T__ERROR_MESSAGE__DEFAULT = 'Обращение к программному интерфейсу службы доставки привело к сбою.';
}