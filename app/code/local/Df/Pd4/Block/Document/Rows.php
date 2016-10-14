<?php
class Df_Pd4_Block_Document_Rows extends Df_Core_Block_Template_NoCache {
	/** @return string */
	public function getCustomerAddressAsCompositeString() {
		return df_e(df_ccc(', '
			,$this->getCustomerAddress()->getPostcode()
			,$this->getCustomerAddress()->getCity()
			,$this->getCustomerAddress()->getStreetAsText()
		));
	}

	/** @return string */
	public function getCustomerName() {
		return df_e(df_ccc(' '
			,$this->order()->getCustomerLastname()
			,$this->order()->getCustomerFirstname()
			,$this->order()->getCustomerMiddlename()
		));
	}

	/** @return string */
	public function getOrderAmountFractionalPartAsString() {
		return df_string($this->getActionDf()->amount()->getFractionalPartAsString());
	}

	/** @return string */
	public function getOrderAmountIntegerPartAsString() {
		return df_string($this->getActionDf()->amount()->getIntegerPart());
	}

	/** @return string */
	public function getRecipientBankAccountNumber() {
		return df_e($this->configA()->getRecipientBankAccountNumber());
	}

	/** @return string */
	public function getRecipientBankId() {return df_e($this->configA()->getRecipientBankId());}

	/** @return string */
	public function getRecipientBankLoro() {return df_e($this->configA()->getRecipientBankLoro());}

	/** @return string */
	public function getRecipientBankName() {
		return df_e($this->configA()->getRecipientBankName());
	}

	/** @return string */
	public function getRecipientName() {return df_e($this->configA()->getRecipientName());}

	/** @return string */
	public function getRecipientTaxNumber() {
		return df_e($this->configA()->getRecipientTaxNumber());
	}

	/** @return string */
	public function getPaymentPurpose() {
		return df_e(strtr($this->configA()->getPaymentPurposeTemplate(), array(
			self::PAYMENT_PURPOSE_TEMPLATE__PARAM__ORDER_ID => $this->order()->getIncrementId()
			,self::PAYMENT_PURPOSE_TEMPLATE__PARAM__ORDER_DATE =>
				df_dts($this->getOrderDate(), Df_Core_Format_Date::FORMAT__RUSSIAN)
		)));
	}

	/** @return int */
	public function getOrderYear() {return df_int(df_dts($this->getOrderDate(), Zend_Date::YEAR));}

	/** @return Df_Pd4_Model_Config_Area_Admin */
	protected function configA() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getActionDf()->getMethod()->configA();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @see Df_Core_Block_Template::defaultTemplate()
	 * @used-by Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/pd4/document/rows.phtml';}

	/** @return Df_Pd4_Model_Request_Document_View */
	private function getActionDf() {return df_h()->pd4()->getDocumentViewAction();}

	/** @return Df_Sales_Model_Order */
	private function order() {return $this->getActionDf()->order();}
	/** @return Df_Sales_Model_Order_Address */
	private function getCustomerAddress() {return $this->order()->getBillingAddress();}

	/** @return Zend_Date */
	private function getOrderDate() {return $this->order()->getDateCreated();}

	const PAYMENT_PURPOSE_TEMPLATE__PARAM__ORDER_ID = '{order.id}';
	const PAYMENT_PURPOSE_TEMPLATE__PARAM__ORDER_DATE = '{order.date}';
}