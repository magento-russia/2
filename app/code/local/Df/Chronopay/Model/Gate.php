<?php
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
				throw new
					Df_Chronopay_Model_Gate_Exception(
						array(
							'messageForCustomer' => $this->getDiagnosticMessageForCustomer()
							,'messageForStatus' => $this->getDiagnosticMessageForStatus()
							,'messageForLog' => $this->getChronopayResponse()->getDiagnosticMessage()
						)
					)
				;
			}

			$payment->setStatus(self::STATUS_APPROVED);
			$payment->setLastTransId($this->getChronopayResponse()->getTransactionId());
		}
		catch(Exception $e) {
			$futureException =
				($e instanceof Df_Chronopay_Model_Gate_Exception)
				? $e
				: new Df_Chronopay_Model_Gate_Exception(array('message' => rm_ets($e)))
			;
			df_notify($futureException->getMessageForLog());
			df_notify($futureException->getMessageForCustomer());
			// The code below does not work for Magento 1.4
			// because Magento 1.4 rollbacks update/insert transaction if exception occured
			if (df_magento_version('1.4', '<')) {
				$payment->setStatus(self::STATUS_ERROR);
				$payment
					->getOrder()
						->setState(Mage_Sales_Model_Order::STATE_HOLDED)
						->addStatusToHistory(
							Mage_Sales_Model_Order::STATE_HOLDED
							,$futureException->getMessageForStatus()
						)
				;
				$payment->getOrder()->save();
			}
			else {
				// TODO: use some tools for logging invalid payment attempt
			}
			throw $futureException;
		}
		return $this;
	}

	/**
	 * @param string $name
	 * @return Df_Chronopay_Block_Gate_Form
	 */
	public function createFormBlock($name) {
		/** @var Df_Chronopay_Block_Gate_Form $result */
		$result = Df_Chronopay_Block_Gate_Form::i($name);
		$result->addData(
			array(
				'method' => $this->_code
				,'payment' => $this->_getData('payment')
			)
		);
		return $result;
	}

	/**
	 * @param $quote
	 * @return bool
	 */
	public function isAvailable($quote = null) {
		$result =
				df_enabled(Df_Core_Feature::CHRONOPAY)
			&&
				parent::isAvailable()
		;
		if (!$result) {
			if ($this->getConfigData('active', ($quote ? $quote->getStoreId() : null))) {
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
			$storeId = $this->getDataUsingMethod('store');
		}
		/** @var string $path */
		$path = rm_config_key('df_payment', $this->getCode(), $field);
		return Mage::getStoreConfig($path, $storeId);
	}

	/** @return array */
	private function getAdditionalPaymentFields() {
		return
			array(
				self::FIELD__CLIENT_LOCAL_TIME
				,self::FIELD__CLIENT_SCREEN_RESOLUTION
			)
		;
	}

	/**
	 * @param Varien_Object $from
	 * @param Varien_Object $to
	 * @return Df_Chronopay_Model_Gate
	 */
	private function assignAdditionalFields(Varien_Object $from, Varien_Object $to) {
		foreach ($this->getAdditionalPaymentFields() as $field) {
			$to->setData($field,$from->getData($field));
		}
		return $this;
	}

	/** @return Df_Chronopay_Model_Settings_Gateway */
	private function cfg() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Chronopay_Model_Settings_Gateway::i(Mage::app()->getStore($this->getStore()))
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param $event
	 * @return void
	 */
	public function sales_convert_quote_payment_to_order_payment($event) {
		$this->assignAdditionalFields($event->getData('quote_payment'), $event->getData('order_payment'));
	}

	/**
	 * @param Varien_Object|array $data
	 * @return Df_Chronopay_Model_Gate
	 */
	public function assignData($data) {
		parent::assignData($data);
		if (!($data instanceof Varien_Object)) {
			$data = new Varien_Object($data);
		}
		$this->assignAdditionalFields($data, $this->getInfoInstance());
		return $this;
	}

	/** @return Df_Directory_Model_Currency */
	public function getChronopayCurrency() {
		return Df_Directory_Model_Currency::ld($this->getChronopayCurrencyCode());
	}

	/** @return bool */
	public function hasVerification() {return true;}

	/** @return string */
	public function sendPurchaseRequest() {
		$putData = tmpfile();
		fwrite($putData, $this->getXml());
		fseek($putData, 0);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://gate.chronopay.com/');
		curl_setopt($ch, CURLOPT_PUT, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_INFILE, $putData);
		curl_setopt($ch, CURLOPT_INFILESIZE, strlen($this->getXml()));
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
			,rm_sprintf(
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

	/** @return Df_Chronopay_Block_Gate_Response */
	private function createResponseBlock() {
		return Df_Chronopay_Block_Gate_Response::i($this->getChronopayResponse());
	}

	/** @return Df_Chronopay_Block_Gate_Request */
	private function createRequestBlock() {
		return Df_Chronopay_Block_Gate_Request::i($this, $this->getInfoInstance());
	}

	/** @return string */
	private function getChronopayCurrencyCode() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->cfg()->getTransactionCurrency();
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getDiagnosticMessageForStatus() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this
					->createResponseBlock()
					->setTemplate(self::DF_TEMPLATE_RESPONSE_ADMIN_STATUS)
					->renderView()
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getDiagnosticMessageForCustomer() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this
					->createResponseBlock()
					->setTemplate(self::DF_TEMPLATE_RESPONSE_CUSTOMER)
					->renderView()
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getXml() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this
					->createRequestBlock()
					->setTemplate(self::DF_TEMPLATE_REQUEST)
					->renderView()
			;
		}
		return $this->{__METHOD__};
	}

	// Шаблон запроса к платёжному шлюзу ChronoPay
	const DF_TEMPLATE_REQUEST = 'df/chronopay/gate/request.xml';
	// Шаблон ответа платёжного шлюза для администратора магазина
	const DF_TEMPLATE_RESPONSE_ADMIN_STATUS = 'df/chronopay/gate/response/admin/status.phtml';
	// Шаблон ответа платёжного шлюза для покупателя
	const DF_TEMPLATE_RESPONSE_CUSTOMER = 'df/chronopay/gate/response/customer.phtml';
	const FIELD__CLIENT_LOCAL_TIME = 'client_local_time';
	const FIELD__CLIENT_SCREEN_RESOLUTION = 'client_screen_resolution';

	/** @return Df_Chronopay_Model_Gate */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}