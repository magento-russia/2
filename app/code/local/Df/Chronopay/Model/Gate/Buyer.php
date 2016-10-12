<?php
class Df_Chronopay_Model_Gate_Buyer extends Df_Core_Model {
	/** @return string */
	public function getCity() {return $this->getBillingAddress()->getCity();}

	/** @return string */
	public function getCountryCode() {
		return $this->getBillingAddress()->getCountryModel()->getIso3Code();
	}

	/** @return string */
	public function getEmail() {
		return rm_first(df_array_clean(
			$this->getOrder()->getCustomerEmail()
			,$this->getBillingAddress()->getEmail()
		));
	}

	/** @return string */
	public function getIpAddress() {
		return
			!is_null(rm_state()->getController())
			? rm_state()->getController()->getRequest()->getClientIp()
			: ''
		;
	}

	/** @return string */
	public function getFirstName() {return df_a($this->getCompositeName(), 0);}

	/** @return string */
	public function getLastName() {return df_a($this->getCompositeName(), 1);}

	/** @return string */
	public function getLocalTime() {
		/** @var string $result */
		$result =
			$this->getPayment()->getData(
				Df_Chronopay_Model_Gate::FIELD__CLIENT_LOCAL_TIME
			)
		;
		df_result_string($result);
		return $result;
	}

	/** @return string */
	public function getPhone() {
 		return $this->getBillingAddress()->getTelephone();
	}

	/** @return string */
	public function getPostCode() {
 		return $this->getBillingAddress()->getPostcode();
	}

	/** @return string */
	public function getRegionCode() {
		/** @var Mage_Directory_Model_Region $region */
		$region = $this->getBillingAddress()->getRegionModel();
		df_assert($region instanceof Mage_Directory_Model_Region);
		/** @var string $result */
		$result = $region->getCode();
		// В БД Magento код у региона есть всегда
		df_result_string($result);
		return $result;
	}

	/** @return string */
	public function getStreetAddress() {
		return $this->getStreetLine1() ? $this->getStreetLine1() : $this->getStreetLine2();
	}

	/** @return string */
	public function getScreenResolution() {
		/** @var string $result */
		$result =
			$this->getPayment()->getData(
				Df_Chronopay_Model_Gate::FIELD__CLIENT_SCREEN_RESOLUTION
			)
		;
		df_result_string($result);
		return $result;
	}

	/** @return string */
	public function getUserAgent() {
		return df_a($_SERVER, "HTTP_USER_AGENT");
	}

	/**
	 * @param string $text
	 * @return string
	 */
	private function __($text) {
		return df_h()->chronopay()-> __ ($text);
	}

	/**
	 * @param string $name
	 * @return Df_Chronopay_Model_Gate_Buyer
	 */
	private function checkNameValidness($name) {
		/** @var string[][] $matches */
		$matches = array();
		/** @var int|bool $matchingResult */
		$matchingResult =
			preg_match_all(
				'#[^A-Z\s]+#mui'
				,$name
				,$matches
				,PREG_PATTERN_ORDER
			)
		;
		df_assert(false !== $matchingResult);
		if (0 < $matchingResult) {
			$invalidSymbols = df_a($matches, 0);
			if (count($invalidSymbols)) {
				df_error(
					implode(
						"\r\n"
						,array_map(
							array($this, "__")
							,array(
								"The cardholder name you entered (“%s”) contains invalid characters: %s."
								,"Only English letters are valid in the cardholder name."
								,"Please return one step back, review your credit card more accurately and type the cardholder name to the payment form straight as it typed on your credit card."
							)
						)
					)
					,$name
					,implode(", ", $invalidSymbols)
				);
			}
		}
		return $this;
	}

	/** @return Mage_Sales_Model_Order_Address */
	private function getBillingAddress() {return $this->getOrder()->getBillingAddress();}

	/** @return string */
	private function getCompositeName() {
		if (!isset($this->{__METHOD__})) {
			$name =
				strtr(
					mb_strtoupper($this->getPayment()->getCcOwner())
					,df_h()->chronopay()->cartholderNameConversionConfig()->getConversionTable()
				)
			;
			$this->checkNameValidness($name);
			// We expect that all name parts besides the last are First Name,
			// and the last part is Last Name
			$exploded = df_trim(explode(' ', $name));
			$countExplodedParts = count($exploded);
			$this->{__METHOD__} =
				array(
					implode(' ', array_slice($exploded, 0, $countExplodedParts - 1))
					,df_a($exploded, $countExplodedParts - 1, '')
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Mage_Sales_Model_Order */
	private function getOrder() {return $this->getPayment()->getData('order');}

	/** @return Mage_Payment_Model_Info */
	private function getPayment() {return $this->_getData(self::P__PAYMENT);}

	/** @return string */
	private function getStreetLine1() {return df_a($this->getBillingAddress()->getStreet(), 0, '');}

	/** @return string */
	private function getStreetLine2() {return df_a($this->getBillingAddress()->getStreet(), 1, '');}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__PAYMENT, 'Mage_Payment_Model_Info');
	}
	const _CLASS = __CLASS__;
	const P__PAYMENT = 'payment';
	/**
	 * @static
	 * @param Mage_Payment_Model_Info $paymentInfo
	 * @return Df_Chronopay_Model_Gate_Buyer
	 */
	public static function i(Mage_Payment_Model_Info $paymentInfo) {
		return new self(array(self::P__PAYMENT => $paymentInfo));
	}
}