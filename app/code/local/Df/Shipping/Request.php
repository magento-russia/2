<?php
namespace Df\Shipping;
abstract class Request extends \Df_Core_Model {
	/**
	 * 2016-10-29
	 * @used-by zuri()
	 * @return string
	 */
	abstract protected function uri();

	/**
	 * @used-by \Df\Shipping\Exception\Request::carrier()
	 * @return Carrier
	 */
	public function getCarrier() {return dfc($this, function() {
		/** @var string $className */
		$className = df_con($this, 'Carrier');
		/** @var Carrier $result */
		$result = new $className;
		df_assert($result instanceof Carrier);
		$result->setStore(df_store());
		return $result;
	});}

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
	 * классов потомков класса @see \Df\Shipping\Request, которым требуется метод @see @see getRate(),
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
	 * @used-by \Df\Shipping\Response::json()
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
			'Модуль: ' . $this->getCarrier()->getTitle()
			,'Адрес: ' . $this->zuri()->__toString()
		];
		/** @var array(string => string) $params */
		$params = $this->paramsPost() + $this->paramsQuery();
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
	public function response() {return dfc($this, function() {return
		Response::i($this, $this->getResponseAsText())
	;});}

	/**
	 * @used-by \Df\Shipping\Request::getDeliveryTime()
	 * @param string|int $value
	 * @return int
	 */
	protected function _filterDeliveryTime($value) {return df_nat($value);}

	/**
	 * @used-by \Df\Shipping\Request::getDeliveryTimeMax()
	 * @param string|int $value
	 * @return int
	 */
	protected function _filterDeliveryTimeMax($value) {return $this->_filterDeliveryTime($value);}

	/**
	 * @used-by \Df\Shipping\Request::getDeliveryTimeMin()
	 * @param string|int $value
	 * @return int
	 */
	protected function _filterDeliveryTimeMin($value) {return $this->_filterDeliveryTime($value);}

	/**
	 * @used-by \Df\Shipping\Request::getRate()
	 * @param float|int|string $value
	 * @return float
	 */
	protected function _filterRate($value) {return df_float_positive($value);}

	/**
	 * Этот метод предназначен для перекрытия потомками.
	 * @used-by \Df\Shipping\Request::getDeliveryTime()
	 * @return int|string
	 */
	protected function _getDeliveryTime() {df_abstract($this); return 0;}

	/**
	 * Этот метод предназначен для перекрытия потомками.
	 * @used-by \Df\Shipping\Request::getRate()
	 * @return float|int|string
	 */
	protected function _getRate() {df_abstract($this); return 0;}

	/**
	 * Этот метод предназначен для перекрытия потомкими.
	 * @see Df_RussianPost_Model_Official_Request_International::adjust()
	 * @used-by client()
	 * @param \Zend_Http_Client $client
	 * @return void
	 */
	protected function adjust(\Zend_Http_Client $client) {}

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
	protected function call($method) {return dfc($this, function($method) {
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
			return $result;
		}
		catch (\Exception $e) {
			// В случае сбоя на стороне службы доставки расчёта тарифа
			// (например, служба Деловые Линии может выдавать ответ:
			// «Сервис временно недоступен. Попробуйте посчитать стоимость доставки чуть позже.»)
			// надо удалить кэш,
			// чтобы при следующем оформлении заказа модуль сделал запрос тарифа заново.
			$this->removeCache();
			df_context('Адрес запроса', $this->zuri()->__toString(), 1);
			/** @var array(string => string) $params */
			$params = $this->paramsPost() + $this->paramsQuery();
			if ($params) {
				df_context('Параметры запроса', df_print_params($params), 2);
			}
			if ($this->postRaw()) {
				df_context('Тело запроса', $this->postRaw(), 3);
			}
			throw $e;
		}
	}, func_get_args());}

	/**
	 * Я думаю, будет нормальным обновлять кэш раз в месяц.
	 * Уж пожизненно его точно не стоит хранить, ибо тарифы служб доставки меняются.
	 * @return \Df_Core_Model_Cache
	 */
	protected function getCache() {return dfc($this, function() {return
		\Df_Core_Model_Cache::i(self::CACHE_TYPE, 30 * 86400)
	;});}

	/**
	 * 2015-02-21
	 * Этот метод предназначен для перекрытия потомками.
	 * @used-by \Df\Shipping\Request::getDeliveryTime()
	 * @return int
	 */
	protected function getDeliveryTimeInternal() {df_abstract($this); return 0;}

	/** @return string */
	protected function getErrorMessage() {return '';}

	/** @return string|array(string => string) */
	protected function getQuery() {return df_clean($this->paramsQuery());}

	/**
	 * @used-by client()
	 * @return array(string => string) 
	 */
	protected function headers() {return [];}	
	
	/** @return string */
	protected function method() {return $this->cfg(self::P__METHOD, \Zend_Http_Client::GET);}

	/** @return array(string => string) */
	protected function paramsPost() {return $this->cfg(self::P__POST, array());}

	/** @return array(string => string) */
	protected function paramsQuery() {return $this->cfg(self::P__QUERY, []);}

	/** @return string */
	protected function postRaw() {return '';}

	/** @return array */
	protected function getRequestConfuguration() {return array();}

	/** @return string */
	protected function getResponseAsTextInternal() {
		/** @var string $result */
		$result = $this->getResponse()->getBody();
		if ($this->needConvertResponseFrom1251ToUtf8()) {
			$result = str_replace('charset=windows-1251', 'charset=utf-8', df_1251_from($result));
		}
		return $result;
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

	/**
	 * 2016-10-29
	 * @used-by zuri()
	 * @return string|null
	 */
	protected function suffix() {return $this[self::P__SUFFIX];}

	/**
	 * Ключ кэширования не должен содержать русские буквы,
	 * потому что когда кэш хранится в файлах, то русские буквы будут заменены на символ «_»,
	 * и имя файла будет выглядеть как «mage---b26_DF_LOCALIZATION_MODEL_MORPHER________».
	 * Чтобы избавиться от русских букв при сохранении уникальности ключа, испольузем функцию md5.
	 *
	 * $this->paramsPost() может вернуть многомерный массив
	 * (например, так происходит в модуле Df_Pec).
	 * Поэтому мы не используем "в лоб" array_merge, а используем http_build_query.
	 *
	 * @return string
	 */
	private function cacheKey() {return dfc($this, function() {return
		dfa_hashm(array_merge(
			[get_class($this), $this->zuri()->__toString()]
			,!$this->isItPost() ? [] : [$this->postRaw() ?: $this->paramsPost()]
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
					$this->client()->setParameterPost($this->paramsPost());
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
							,http_build_query($this->paramsPost(), '', '&')
						)
					);
				}
			}
		}
		/** @var \Zend_Http_Response $result */
		$result = $this->client()->request($this->method());
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
				'Сервер службы доставки «%s»'
				. ' перенаправил запрос «%s» с адреса «%s» на неожиданный адрес «%s».'
				, $this->getCarrier()->getTitle()
				, get_class($this)
				, $this->zuri()->__toString()
				, $this->client()->getUri()->__toString()
			);
		}
		return $result;
	});}

	/**
	 * @return string
	 * @throws \Df\Shipping\Exception\NoResponse
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
				throw new \Df\Shipping\Exception\NoResponse($e, $this);
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

	/** @return \Zend_Uri_Http */
	private function zuri() {return dfc($this, function() {
		/** @var \Zend_Uri_Http $result */
		$result = \Zend_Uri::factory($this->uri());
		$result->setPath($result->getPath() . $this->suffix());
		$result->setQuery($this->getQuery());
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
	/** @var bool */
	private $_responseFailureChecked = false;
	/**
	 * @used-by \Df_Shipping_Setup_2_30_0::_process()
	 * @used-by getCache()
	 */
	const CACHE_TYPE = 'rm_shipping';
	const P__METHOD = 'request_method';
	const P__POST = 'post_params';
	const P__QUERY = 'query_params';
	const P__SUFFIX = 'suffix';
}