<?php
class Df_Parser_Model_Browser extends Df_Core_Model {
	/**
	 * @param Zend_Uri|string $uri
	 * @param bool $throwOnError [optional]
	 * @return string
	 */
	public function getPage($uri, $throwOnError = true) {
		if ($uri instanceof Zend_Uri) {
			$uri = $uri->getUri();
		}
		if (!isset($this->{__METHOD__}[$uri])) {
			/** @var string $cacheKey */
			$cacheKey = $this->getCache()->makeKey(__METHOD__, array($this->getArea(), md5($uri)));
			/** @var string $result */
			$result = $this->getCache()->loadData($cacheKey);
			if (!$result) {
				df_h()->parser()->log('Пытаюсь загрузить с сервера страницу «%s».', $uri);
				/** @var int $attemptCount */
				$attemptCount = 0;
				/** @var float $sleepIntervalInSeconds */
				$sleepIntervalInSeconds = 0.2;
				while (!$this->isValid($result) && ($attemptCount < $this->getMaxAttemptCount())) {
					/** @var string|bool $contents */
					$result = @file_get_contents($uri);
					if (!$this->isValid($result)) {
						$attemptCount++;
						df_h()->parser()->log(
							!$result
							?  'Не удалось загрузить с сервера страницу «%s». Делаю попытку №%d.'
							: 'Страница «%s» успешно загружена, но не прошла валидацию. Делаю попытку №%d.'
							, $uri, $attemptCount+1
						);
						usleep($sleepIntervalInSeconds * 1000000);
						$sleepIntervalInSeconds *= 2;
					}
				}
				$this->getCache()->saveData($cacheKey, $result);
				if (!$this->isValid($result)) {
					df_h()->parser()->log('Не удалось загрузить с сервера страницу «%s». Отбой.', $uri);
					if ($throwOnError) {
						$this->throwPageIsNotExist($uri);
					}
					else {
						$result = '';
					}
				}
			}
			$this->{__METHOD__}[$uri] = $result;
		}
		return $this->{__METHOD__}[$uri];
	}

	/**
	 * @param string $uri
	 * @return void
	 */
	public function throwPageIsNotExist($uri) {df_error('Не могу скачать страницу: ' . $uri);}

	/** @return string */
	private function getArea() {return $this->cfg(self::P__AREA);}

	/** @return Df_Core_Model_Cache */
	private function getCache() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Core_Model_Cache::i(null, true);
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	private function getMaxAttemptCount() {
		return $this->cfg(self::P__MAX_ATTEMPT_COUNT, self::DEFAULT__MAX_ATTEMPT_COUNT);
	}

	/** @return Zend_Validate_Interface|null */
	private function getValidator() {return $this->cfg(self::P__VALIDATOR);}

	/**
	 * @param mixed $contents
	 * @return bool
	 */
	private function isValid($contents) {
		return $contents && (is_null($this->getValidator()) || $this->getValidator()->isValid($contents));
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__AREA, DF_V_STRING)
			->_prop(self::P__MAX_ATTEMPT_COUNT, DF_V_INT, false)
			->_prop(self::P__VALIDATOR, 'Zend_Validate_Interface', false)
		;
	}
	const _C = __CLASS__;
	const DEFAULT__MAX_ATTEMPT_COUNT = 5;
	const P__AREA = 'area';
	const P__MAX_ATTEMPT_COUNT = 'max_attempt_count';
	const P__VALIDATOR = 'validator';

	/**
	 * 2015-02-07
	 * Обратите внимание, что в сигнатуре метода/функции
	 * для параметров объектного типа со значением по умолчанию null
	 * мы вправе, тем не менее, указывать тип-класс.
	 * Проверял на всех поддерживаемых Российской сборкой Magento версиях интерпретатора PHP,
	 * сбоев нет:
	 * http://3v4l.org/ihOFp
	 * @static
	 * @param string|null $area [optional]
	 * @param Zend_Validate_Interface|null $validator [optional]
	 * @param int|null $maxAttemptCount [optional]
	 * @return Df_Parser_Model_Browser
	 */
	public static function i(
		$area = null, Zend_Validate_Interface $validator = null, $maxAttemptCount = null
	) {
		return new self(array(
			self::P__AREA => $area
			, self::P__VALIDATOR => $validator
			, self::P__MAX_ATTEMPT_COUNT => $maxAttemptCount
		));
	}
	/**
	 * @static
	 * @param string|null $area [optional]
	 * @return Df_Parser_Model_Browser
	 */
	public static function s($area = null) {
		/** @var array(string => Df_Parser_Model_Browser) $instances */
		static $instances;
		if (!isset($instances[$area])) {
			$instances[$area] = new self(array(self::P__AREA => $area));
		}
		return $instances[$area];
	}
}