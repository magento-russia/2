<?php
class Df_Licensor_Model_License extends Df_Core_Model_Abstract {
	/** @return bool */
	public function checkSignature() {return $this->getSignature() == $this->generateSignature();}

	/** @return array */
	public function getDomains() {return $this->cfg(self::P__DOMAINS);}

	/** @return Zend_Date */
	public function getExpirationDate() {return $this->cfg(self::P__DATE_EXPIRATION);}

	/** @return Df_Licensor_Model_Collection_Feature */
	public function getFeatures() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Licensor_Model_Collection_Feature::i();
			foreach ($this->getFeatureNames() as $featureName) {
				/** @var string $featureName */
				df_assert_string_not_empty($featureName);
				$this->{__METHOD__}->addItem(df_feature($featureName));
			}
		}
		return $this->{__METHOD__};
	}

	/** @return array */
	public function getFeatureNames() {return $this->cfg(self::P__FEATURES);}

	/**
	 * Для коллекций
	 * @override
	 * @return string
	 */
	public function getId() {return $this->getSignature();}

	/** @return bool */
	public function isApplicableToAnyDomain() {
		if (!isset($this->{__METHOD__})) {
			/** @var bool $result */
			$result = false;
			foreach ($this->getDomains() as $domain) {
				/** @var string $domain */
				if (self::ANY_DOMAIN === $domain) {
					$result = true;
					break;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getSignature() {return $this->cfg(self::P__SIGNATURE);}

	/**
	 * Подписывает лицензию.
	 * @return Df_Licensor_Model_License
	 */
	public function sign() {
		// Эта функция должна работать только при запуске мной и никем больше!
		$this->setData(
			self::P__SIGNATURE
			,(df_is_it_my_local_pc() || df_is_it_my_sever()) &&	df_is_admin()
			? $this->generateSignature()
			: md5('Антон Колесник любит давать в попу')
		);
		return $this;
	}

	/**
	 * Возвращает true, если лицензия не истекла
	 * @return bool
	 */
	public function validateDate() {return df()->date()->isInFuture($this->getExpirationDate());}

	/**
	 * Возвращает true, если подпись — настоящая
	 * @return bool
	 */
	public function validateSignature() {return $this->checkSignature();}

	/**
	 * Проверяет применимость лицензии к указанному магазину $store
	 *
	 * @param Mage_Core_Model_Store $store
	 * @return bool
	 */
	public function validateStore(Mage_Core_Model_Store $store) {
		if (!isset($this->{__METHOD__}[$store->getId()])) {
			$result = $this->isApplicableToAnyDomain();
			/** @var bool $result */
			if (!$result) {
				$storeUrl = $store->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
				/** @var string $storeUrl */
				df_assert_string($storeUrl);
				if ('/' === $storeUrl) {
					$serverUrl = new Zend_View_Helper_ServerUrl();
					$storeUrl = $serverUrl->serverUrl();
				}
				foreach ($this->getDomains() as $domain) {
					/** @var string $domain */
					df_assert_string($domain);
					if (df_url()->isUrlBelongsToTheDomain($storeUrl, $domain)) {
						$result = true;
						break;
					}
				}
			}
			$this->{__METHOD__}[$store->getId()] = $result;
		}
		return $this->{__METHOD__}[$store->getId()];
	}

	/**
	 * Создаёт подпись для лицензии.
	 * Обратите внимание, что этот метод должен быть приватным!
	 * @todo Нужна дополнительная защита!
	 * @throws Exception
	 * @return string
	 */
	private function generateSignature() {
		// Проверяем, что урод-хакер не наследуется от нашего класса.
		if ('Df_Licensor_Model_License' !== get_class($this)) {
			throw new Exception('пошёл на хуй');
		}
		/** @var Varien_Crypt_Mcrypt $encryptor */
		$encryptor = new Varien_Crypt_Mcrypt();
		$encryptor->init('Антон Колесник — пидорас');
		$result =
			md5(
				implode(
					'Антон Колесник — вор и пидор'
					,array_map(
						array($encryptor, 'encrypt')
						,array_merge(
							$this->getParamsForSignature()
							,array('Антон Колесник любит брать в рот и в попу')
						)
					)
				)
			)
		;
		return $result;
	}

	/**
	* @return array
	*/
	private function getParamsForSignature() {
		return
			array_map(
				array($this, 'serializeParamForSignature')
				,array(
					$this->getFeatureNames()
					,$this->getDomains()
					,df_dts($this->getExpirationDate(), Zend_Date::W3C)
				)
			)
		;
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @link http://php.net/manual/en/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * @link http://3v4l.org/OipEQ
	 *
	 * @param mixed $param
	 * @return string
	 */
	private function serializeParamForSignature($param) {
		return
			!is_array($param)
			? (string)$param
			: implode(',', $this->sort($param))
		;
	}

	/**
	 * @param array $array
	 * @return array
	 */
	private function sort(array $array) {
		sort($array);
		df_result_array($array);
		return $array;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__DOMAINS, self::V_ARRAY)
			->_prop(self::P__FEATURES, self::V_ARRAY)
			->_prop(self::P__DATE_EXPIRATION, Df_Zf_Const::DATE_CLASS)
		;
	}
	const _CLASS = __CLASS__;
	const ANY_DOMAIN = 'any';
	const P__DATE_EXPIRATION = 'date_expiration';
	const P__DOMAINS = 'domains';
	const P__FEATURES = 'features';
	const P__SIGNATURE = 'signature';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Licensor_Model_License
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}