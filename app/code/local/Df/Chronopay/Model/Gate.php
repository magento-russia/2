<?php
/** @method int getStore() */
class Df_Chronopay_Model_Gate extends Mage_Payment_Model_Method_Cc {
	protected $_code  = 'chronopay_gate';
	protected $_formBlockType = 'df_chronopay/gate_form';
	protected $_infoBlockType = 'df_chronopay/gate_info';
	protected $_isGateway			   = true;
	protected $_canAuthorize			= false;
	protected $_canCapture			  = true;
	protected $_canCapturePartial	   = false;
	protected $_canRefund			   = false;
	protected $_canVoid				 = false;
	protected $_canUseInternal		  = false;
	protected $_canUseCheckout		  = true;
	protected $_canUseForMultishipping  = false;
	protected $_order = null;

	/**
	 * @throws Exception|Df_Chronopay_Model_Gate_Exception|?
	 * @param Varien_Object $payment
	 * @param float $amount
	 * @return Df_Chronopay_Model_Gate
	 */
	public function capture(Varien_Object $payment, $amount)
	{
		parent::capture($payment, $amount);
		try {
			if (0 != $this->getChronopayResponse()->getCode()) {
				df_error(new Df_Chronopay_Model_Gate_Exception(array(
					'messageC' => Df_Chronopay_Block_Gate_Response::r(
						$this->getChronopayResponse(), 'df/chronopay/gate/response/customer.phtml'
					)
					,'messageForStatus' => Df_Chronopay_Block_Gate_Response::r(
						$this->getChronopayResponse(), 'df/chronopay/gate/response/admin/status.phtml'
					)
					,'messageL' => $this->getChronopayResponse()->getDiagnosticMessage()
				)));
			}
			/** @noinspection PhpUndefinedMethodInspection */
			$payment->setStatus(self::STATUS_APPROVED);
			/** @noinspection PhpUndefinedMethodInspection */
			$payment->setLastTransId($this->getChronopayResponse()->getTransactionId());
		}
		catch (Exception $e) {
			$futureException =
				$e instanceof Df_Chronopay_Model_Gate_Exception
				? $e
				: new Df_Chronopay_Model_Gate_Exception(array('message' => df_ets($e)))
			;
			df_notify($futureException->getMessageForLog());
			df_notify($futureException->getMessageForCustomer());
			// The code below does not work for Magento 1.4
			// because Magento 1.4 rollbacks update/insert transaction if exception occured
			if (df_magento_version('1.4', '<')) {
				/** @noinspection PhpUndefinedMethodInspection */
				$payment->setStatus(self::STATUS_ERROR);
				/** @noinspection PhpUndefinedMethodInspection */
				$payment
					->getOrder()
						->setState(Mage_Sales_Model_Order::STATE_HOLDED)
						->addStatusToHistory(
							Mage_Sales_Model_Order::STATE_HOLDED
							,$futureException->getMessageForStatus()
						)
				;
				/** @noinspection PhpUndefinedMethodInspection */
				$payment->getOrder()->save();
			}
			else {
				// TODO: use some tools for logging invalid payment attempt
			}
			df_error($futureException);
		}
		return $this;
	}

	/**
	 * @param $quote
	 * @return bool
	 */
	public function isAvailable($quote = null) {
		$result = parent::isAvailable();
		if (!$result) {
			/** @noinspection PhpUndefinedMethodInspection */
			if ($this->getConfigData('active', ($quote ? $quote->getStoreId() : null))) {
				/** @noinspection PhpUndefinedMethodInspection */
				df_assert(
					$this->getConfigData('cctypes', ($quote ? $quote->getStoreId() : null))
					,'Администратору: выберите для способа оплаты ChoronoPay Gateway'
					.' хотя бы один тип банковских карт.'
				)
				;
			}
		}
		if ($result) {
			$this->checkChronopayCurrencyEnabled();
		}
		return $result;
	}

	/**
	 * @param string $field
	 * @param null|int $storeId
	 * @return string
	 */
	public function getConfigData($field, $storeId = null) {
		if (null === $storeId) {
			$storeId = $this->getStore();
		}
		/** @var string $path */
		$path = df_cc_path('df_payment', $this->getCode(), $field);
		return df_cfg($path, $storeId);
	}

	/**
	 * @param Varien_Object $from
	 * @param Varien_Object $to
	 * @return void
	 */
	private function assignAdditionalFields(Varien_Object $from, Varien_Object $to) {
		$to->addData(dfa_select($from->getData(), array(
			self::FIELD__CLIENT_LOCAL_TIME, self::FIELD__CLIENT_SCREEN_RESOLUTION
		)));
	}

	/** @return Df_Chronopay_Model_Settings_Gateway */
	private function cfg() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Chronopay_Model_Settings_Gateway::i(df_store($this->getStore()));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param Varien_Object|array $data
	 * @return Df_Chronopay_Model_Gate
	 */
	public function assignData($data) {
		parent::assignData($data);
		if (!$data instanceof Varien_Object) {
			$data = new Varien_Object($data);
		}
		$this->assignAdditionalFields($data, $this->getInfoInstance());
		return $this;
	}

	/** @return Df_Directory_Model_Currency */
	public function getChronopayCurrency() {return df_currency($this->getChronopayCurrencyCode());}

	/** @return bool */
	public function hasVerification() {return true;}

	/** @return string */
	public function sendPurchaseRequest() {
		$putData = tmpfile();
		/** @var string $xml */
		$xml = Df_Chronopay_Block_Gate_Request::r($this, $this->getInfoInstance());
		fwrite($putData, $xml);
		fseek($putData, 0);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://gate.chronopay.com/');
		curl_setopt($ch, CURLOPT_PUT, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_INFILE, $putData);
		curl_setopt($ch, CURLOPT_INFILESIZE, strlen($xml));
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
		$result = curl_exec($ch);
		fclose($putData);
		curl_close($ch);
		return $result;
	}

	/** @return Df_Chronopay_Model_Gate */
	private function checkChronopayCurrencyEnabled() {
		df_assert(
			$this->getChronopayCurrency()->getCode()
			,'Please, select the transaction currency for ChronoPay'
		);
		df_assert_in(
			$this->getChronopayCurrency()->getCode()
			,Df_Directory_Model_Currency::i()->getConfigAllowCurrencies()
			,sprintf(
				'ChronoPay currency %s must be set as allowed in Magento'
				. '\nConfiguration → General → Currency Setup → Currency Options → Allowed currencies'
				,$this->getChronopayCurrency()->getCode()
			)
		);
		return $this;
	}

	/** @return Df_Chronopay_Model_Gate_Response */
	private function getChronopayResponse() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Chronopay_Model_Gate_Response::i($this->sendPurchaseRequest());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getChronopayCurrencyCode() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->cfg()->getTransactionCurrency();
		}
		return $this->{__METHOD__};
	}

	const FIELD__CLIENT_LOCAL_TIME = 'client_local_time';
	const FIELD__CLIENT_SCREEN_RESOLUTION = 'client_screen_resolution';

	/** @return Df_Chronopay_Model_Gate */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}