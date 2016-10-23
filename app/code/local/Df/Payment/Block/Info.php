<?php
/**
 * Обратите внимание, что класс @see Df_Payment_Block_Info не унаследован от @see Mage_Payment_Block_Info
 * но реализует полностью его интерфейс.
 * Намеренно наследуемся от @see Df_Core_Block_Template,
 * чтобы пользоваться всеми возможностями этого класса.
 */
class Df_Payment_Block_Info extends Df_Core_Block_Template_NoCache {
	/**
	 * @override
	 * @return string
	 */
	public function getArea() {return Df_Core_Const_Design_Area::FRONTEND;}

	/**
	 * Заимствовано из @see Mage_Payment_Block_Info::getChildPdfAsArray()
	 * @return string[]
	 */
	public function getChildPdfAsArray() {
		/** @var string[] $result */
		$result = array();
		foreach ($this->getChild() as $child) {
			/** @var Mage_Core_Block_Abstract $child */
			/**
			 * 2015-02-02
			 * Метод ядра @see Mage_Payment_Block_Info::getChildPdfAsArray()
			 * использует здесь код
				is_callable(array($child, 'toPdf')
			 * Я так понимаю, что использовать @see is_callable() в Magento не стоит,
			 * потому что наличие @see Varien_Object::__call()
			 * приводит к тому, что @see is_callable() всегда возвращает true.
			 *
			 * Обратите внимание, что @uses method_exists(), в отличие от @see is_callable(),
			 * не гарантирует публичную доступность метода:
			 * т.е. метод может у класса быть, но вызывать его всё равно извне класса нельзя,
			 * потому что он имеет доступность private или protected.
			 * Пока эта проблема никак не решена.
			 */
			/**
			 * @uses Mage_Payment_Block_Info::toPdf()
			 * @uses Df_Payment_Block_Info::toPdf()
			 */
			if (method_exists($child, 'toPdf')) {
				$result[] = $child->toPdf();
			}
		}
		return $result;
	}

	/**
	 * Заимствовано из:
	 * @see Mage_Payment_Block_Info::getInfo()
	 * @return Mage_Payment_Model_Info|Mage_Sales_Model_Order_Payment
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
	 * Заимствовано из @see Mage_Payment_Block_Info::getIsSecureMode
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
		return df_is_admin($method->getDataUsingMethod('store'));
	}

	/**
	 * заимствовано из Mage_Payment_Block_Info
	 * @see Mage_Payment_Block_Info::getMethod
	 * @override
	 * @return Df_Payment_Method
	 */
	public function method() {return $this->getInfo()->getMethodInstance();}

	/** @return string */
	public function getMethodTitle() {return $this->method()->getTitle();}

	/**
	 * заимствовано из Mage_Payment_Block_Info
	 * @see Mage_Payment_Block_Info::getSpecificInformation
	 * @return array
	 */
	public function getSpecificInformation() {return $this->_prepareSpecificInformation()->getData();}

	/**
	 * Заимствовано из @see Mage_Payment_Block_Info::getValueAsArray()
	 * @param string|mixed|string[]|mixed[] $value
	 * @param bool $escapeHtml [optional]
	 * @return string[]|mixed[]
	 */
	public function getValueAsArray($value, $escapeHtml = false) {
		/** @var string[]|mixed[] $result */
		if (!$value) {
			$result = array();
		}
		else {
			$result = df_array($value);
			if ($escapeHtml) {
				foreach ($result as $key => $item) {
					/** @var string|int $key */
					/** @var string|mixed $item */
					$result[$key] = df_e($item);
				}
			}
		}
		return $result;
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
			Mage::dispatchEvent('payment_info_block_prepare_specific_information', array(
				'transport' => $transport
				,'payment'   => $this->getInfo()
				,'block'     => $this
			));
			$this->_paymentSpecificInformation = $transport;
		}
		return $this->_paymentSpecificInformation;
	}

	/**
	 * @override
	 * @see Df_Core_Block_Template::defaultTemplate()
	 * @used-by Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/payment/info.phtml';}

	/**
	 * @used-by Df_Pd4_Block_Info::capableLinkToOrder()
	 * @used-by Df_Pd4_Block_Info::getLinkBlock()
	 * @return Df_Sales_Model_Order|null
	 */
	protected function order() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Payment_Model_Info|Mage_Sales_Model_Order_Payment $paymentInfo */
			$paymentInfo = $this->getInfo();
			$this->{__METHOD__} = df_n_set(
				!$paymentInfo instanceof Mage_Sales_Model_Order_Payment
				? null
				: $paymentInfo->getOrder()
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @see Mage_Payment_Block_Info::toPdf()
	 * @return string
	 */
	public function toPdf() {
		$this->setTemplate('payment/info/pdf/default.phtml');
		return $this->toHtml();
	}

	/** @var Varien_Object|null */
	protected $_paymentSpecificInformation = null;

	/** @used-by Df_Payment_Method::getInfoBlockType() */

}