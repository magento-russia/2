<?php
/**
 * Обратите внимание, что класс Df_Payment_Block_Info не унаследован от Mage_Payment_Block_Info,
 * но реализует полностью его интерфейс.
 * Намеренно наследуемся от Df_Core_Block_Template,
 * чтобы пользоваться всеми возможностями этого класса.
 */
class Df_Payment_Block_Info extends Df_Core_Block_Template_NoCache {
	/**
	 * @override
	 * @return string
	 */
	public function getArea() {return Df_Core_Const_Design_Area::FRONTEND;}

	/**
	 * заимствовано из Mage_Payment_Block_Info
	 * @see Mage_Payment_Block_Info::getChildPdfAsArray
	 * @return array
	 */
	public function getChildPdfAsArray() {
		$result = array();
		foreach ($this->getChild() as $child) {
			if (is_callable(array($child, 'toPdf'))) {
				$result[] = call_user_func(array($child, 'toPdf'));
			}
		}
		return $result;
	}

	/**
	 * заимствовано из Mage_Payment_Block_Info
	 * @see Mage_Payment_Block_Info::getInfo
	 * @return Mage_Payment_Model_Info
	 */
	public function getInfo() {
		/** @var Mage_Payment_Model_Info $result */
		$result = $this->getData('info');
		if (!($result instanceof Mage_Payment_Model_Info)) {
			Mage::throwException($this->__('Cannot retrieve the payment info model object.'));
		}
		return $result;
	}

	/**
	 * заимствовано из Mage_Payment_Block_Info
	 * @see Mage_Payment_Block_Info::getIsSecureMode
	 * @return bool
	 */
	public function getIsSecureMode() {
		if ($this->hasData('is_secure_mode')) {
			return !!$this->_getData('is_secure_mode');
		}
		if (!$payment = $this->getInfo()) {
			return true;
		}
		/** @var Mage_Payment_Model_Method_Abstract $method */
		if (!$method = $payment->getMethodInstance()) {
			return true;
		}
		return !Mage::app()->getStore($method->getDataUsingMethod('store'))->isAdmin();
	}

	/**
	 * заимствовано из Mage_Payment_Block_Info
	 * @see Mage_Payment_Block_Info::getMethod
	 * @override
	 * @return Df_Payment_Model_Method_Base
	 */
	public function getMethod() {return $this->getInfo()->getMethodInstance();}

	/** @return string */
	public function getMethodTitle() {return $this->getMethod()->getTitle();}

	/**
	 * заимствовано из Mage_Payment_Block_Info
	 * @see Mage_Payment_Block_Info::getSpecificInformation
	 * @return array
	 */
	public function getSpecificInformation() {
		return $this->_prepareSpecificInformation()->getData();
	}

	/**
	 * заимствовано из Mage_Payment_Block_Info
	 * @see Mage_Payment_Block_Info::getValueAsArray
	 * @param mixed $value
	 * @param bool $escapeHtml [optional]
	 * @return mixed[]
	 */
	public function getValueAsArray($value, $escapeHtml = false) {
		if (empty($value)) {
			return array();
		}
		if (!is_array($value)) {
			$value = array($value);
		}
		if ($escapeHtml) {
			foreach ($value as $_key => $_val) {
				$value[$_key] = $this->escapeHtml($_val);
			}
		}
		return $value;
	}

	/**
	 * заимствовано из Mage_Payment_Block_Info
	 * @see Mage_Payment_Block_Info::_prepareSpecificInformation
	 * @param Varien_Object|array $transport
	 * @return Varien_Object
	 */
	protected function _prepareSpecificInformation($transport = null) {
		if (null === $this->_paymentSpecificInformation) {
			if (null === $transport) {
				$transport = new Varien_Object;
			}
			elseif (is_array($transport)) {
				$transport = new Varien_Object($transport);
			}
			Mage::dispatchEvent(
				'payment_info_block_prepare_specific_information'
				,array(
					'transport' => $transport
					,'payment'   => $this->getInfo()
					,'block'     => $this
				)
			);
			$this->_paymentSpecificInformation = $transport;
		}
		return $this->_paymentSpecificInformation;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getDefaultTemplate() {return 'df/payment/info.phtml';}

	/** @return Mage_Sales_Model_Order|null */
	protected function getOrder() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Payment_Model_Info $paymentInfo */
			$paymentInfo = $this->getInfo();
			/** @var Mage_Sales_Model_Order|null $result */
			$result =
				!($paymentInfo instanceof Mage_Sales_Model_Order_Payment)
				? null
				: $paymentInfo->getDataUsingMethod('order')
			;
			if (!is_null($result)) {
				df_assert($result instanceof Mage_Sales_Model_Order);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return bool
	 */
	protected function needCaching() {return false;}

	/**
	 * заимствовано из Mage_Payment_Block_Info
	 * @see Mage_Payment_Block_Info::toPdf
	 * @return string
	 */
	public function toPdf() {
		$this->setTemplate('payment/info/pdf/default.phtml');
		return $this->toHtml();
	}

	/** @var Varien_Object|null */
	protected $_paymentSpecificInformation = null;

	const _CLASS = __CLASS__;
}