<?php
abstract class Df_Shipping_Model_Locator extends Df_Core_Model {
	/**
	 * @abstract
	 * @return string
	 * @throws Exception
	 */
	abstract public function getResult();

	/**
	 * @param string $message
	 * @return string
	 */
	public function evaluateMessage($message) {
		/**
		 * @see Df_Spsr_Model_Validator_Origin
		 * Там у нас нет объекта $this->getCarrier()
		 */
		return
			!$this->getCarrier()
			? $message
			: $this->getCarrier()->evaluateMessage($message, $this->getMessageVariables())
		;
	}

	/** @return Df_Shipping_Model_Carrier|null */
	protected function getCarrier() {
		/**
		 * @see Df_Spsr_Model_Validator_Origin
		 * Там у нас нет объекта $this->getRequest()
		 */
		return !$this->getRequest() ? null : $this->getRequest()->getCarrier();
	}

	/** @return string|null */
	protected function getCity() {
		/** @var string|null $result */
		$result = $this->cfg(self::P__CITY);
		if (is_null($result)) {
			/**
			 * В справочнике субъектов федерации некоторых служб доставки
			 * (в частности, EMS) отсутствуют регионы Москва и Санкт-Петербург.
			 *
			 * Стандартный калькулятор стоимости доставки на странице корзины
			 * не позволяет указать город, но позволяет указать субъект федерации.
			 *
			 * И, когда покупатель указывает субъект федерации "Москва",
			 * то модуль не в состоянии рассчитать стоимость доставки,
			 * потому что субъекта федерации "Москва" в справочнике EMS отсутствует,
			 * а информации о городе в данном случае нет.
			 *
			 * Чтобы всё-таки рассчитать тариф в данной ситуации,
			 * искусственно устанавливаем в качестве города Москву,
			 * когда в качестве субъекта федерации выбрана Москва.
			 */
			if (in_array($this->getRegionName(), array('Москва', 'Санкт-Петербург'))) {
				$result = $this->getRegionName();
			}
		}
		if (!is_null($result)) {
			df_result_string($result);
		}
		return $result;
	}

	/** @return string|null */
	protected function getCountryId() {return $this->cfg(self::P__COUNTRY_ID);}

	/** @return Df_Directory_Model_Country */
	protected function getCountry() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Directory_Model_Country::i();
			$this->{__METHOD__}->loadByCode($this->getCountryId());
		}
		return $this->{__METHOD__};
	}

	/** @return string|null */
	protected function getCountryName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(
				!$this->getCountry()->getId()
				? null
				: $this->getCountry()->getName()
			);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return string|null */
	protected function getCountryNameEnglish() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(
				!$this->getCountry()->getId()
				? null
				: df_h()->directory()->country()->getNameEnglish($this->getCountry())
			);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return array(string => string) */
	protected function getMessageVariables() {return $this->getRequest()->getMessageVariables();}

	/** @return int|null */
	protected function getRegionId() {
		/** @var int|null $result */
		$result = $this->cfg(self::P__REGION_ID);
		if (!is_null($result)) {
			df_result_integer($result);
		}
		return $result;
	}

	/** @return string|null */
	protected function getRegionName() {
		return
			!$this->getRegionId()
			? $this->cfg(self::P__REGION_NAME)
			: df_h()->directory()->getRegionNameById($this->getRegionId())
		;
	}

	/**
	 * @see Df_Spsr_Model_Validator_Origin
	 * Там у нас нет объекта Df_Shipping_Model_Rate_Request
	 * @return Df_Shipping_Model_Rate_Request|null
	 */
	protected function getRequest() {return $this->cfg(self::P__REQUEST);}

	/** @return bool */
	protected function isDestination() {return $this->cfg(self::P__IS_DESTINATION, true);}

	/**
	 * @throws Df_Core_Exception_Client
	 * @param string $message
	 * @return void
	 */
	protected function throwException($message) {
		/**
		 * Обратите внимание, что функция func_get_args() не может быть параметром другой функции.
		 * @var mixed[] $arguments
		 */
		$arguments = func_get_args();
		/** @var string $message */
		$message = rm_sprintf($arguments);
		$message = $this->evaluateMessage($message);
		/**
		 * В административной части @see df_no_escape не нужно и даже вредно,
		 * ибо там тэг {#rm-no-escape#} обработан не будет!
		 * В административной части мы можем быть в контексте, например,
		 * @see Df_Spsr_Model_Validator_Origin.
		 */
		if (!df_is_admin()) {
			$message = df_no_escape($message);
		}
		df_error($message);
	}

	/**
	 * @throws Df_Core_Exception_Client
	 */
	protected function throwExceptionInvalidDestination() {
		$this->throwException(
			'Доставка <b>{в место доставки}</b>'
			.' {название службы и способа доставки в творительном падеже} невозможна.'
		);
	}

	/**
	 * @throws Df_Core_Exception_Client
	 */
	protected function throwExceptionInvalidOrigin() {
		$this->throwException(
			'Доставка <b>{из места отправки}</b>'
			.' {название службы и способа доставки в творительном падеже} невозможна.'
		);
	}

	/** @return Df_Shipping_Model_Locator */
	protected function throwExceptionInvalidLocation() {
		if ($this->isDestination()) {
			$this->throwExceptionInvalidDestination();
		}
		else {
			$this->throwExceptionInvalidOrigin();
		}
		return $this;
	}

	/** @throws Df_Core_Exception_Client */
	public function throwExceptionNoCityDestination() {$this->throwException('Укажите город.');}

	/** @throws Df_Core_Exception_Client */
	public function throwExceptionNoCityOrigin() {
		$this->throwException('Администратор должен указать город, где расположен склад магазина.');
	}

	/** @throws Df_Core_Exception_Client */
	public function throwExceptionNoRegionOrigin() {
		$this->throwException(
			df_is_admin()
			? "Администратор должен <a href='http://magento-forum.ru/topic/3575/'>указать область</a>,"
			. " где расположен склад магазина."
			: 'Администратор должен указать область, где расположен склад магазина.'
		);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__CITY, self::V_STRING, false)
			->_prop(self::P__COUNTRY_ID, self::V_STRING, false)
			->_prop(self::P__IS_DESTINATION, self::V_BOOL, false)
			->_prop(self::P__REGION_ID, self::V_INT, false)
			->_prop(self::P__REGION_NAME, self::V_STRING, false)
			->_prop(self::P__REQUEST, Df_Shipping_Model_Rate_Request::_CLASS, false)
		;
	}
	const _CLASS = __CLASS__;
	const P__CITY = 'city';
	const P__COUNTRY_ID = 'country_id';
	const P__IS_DESTINATION = 'is_destination';
	const P__REGION_ID = 'region_id';
	const P__REGION_NAME = 'region_name';
	const P__REQUEST = 'request';
}