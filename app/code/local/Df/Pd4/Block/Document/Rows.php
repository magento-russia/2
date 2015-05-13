<?php
class Df_Pd4_Block_Document_Rows extends Df_Core_Block_Template_NoCache {
	/** @return string */
	public function getCustomerAddressAsCompositeString() {
		return $this->escapeHtml(rm_concat_clean(', '
			,$this->getCustomerAddress()->getData(Df_Sales_Const::ORDER_ADDRESS__PARAM__POSTCODE)
			,$this->getCustomerAddress()->getData(Df_Sales_Const::ORDER_ADDRESS__PARAM__CITY)
			,$this->getCustomerAddress()->getData(Df_Sales_Const::ORDER_ADDRESS__PARAM__STREET)
		));
	}

	/** @return string */
	public function getCustomerName() {
		return $this->escapeHtml(rm_concat_clean(' '
			,$this->getOrder()->getData(Df_Sales_Const::ORDER_PARAM__CUSTOMER_LASTNAME)
			,$this->getOrder()->getData(Df_Sales_Const::ORDER_PARAM__CUSTOMER_FIRSTNAME)
			,$this->getOrder()->getData(Df_Sales_Const::ORDER_PARAM__CUSTOMER_MIDDLENAME)
		));
	}

	/** @return string */
	public function getOrderAmountFractionalPartAsString() {
		return df_string($this->getActionDf()->getAmount()->getFractionalPartAsString());
	}

	/** @return string */
	public function getOrderAmountIntegerPartAsString() {
		return df_string($this->getActionDf()->getAmount()->getIntegerPart());
	}

	/** @return string */
	public function getRecipientBankAccountNumber() {
		return $this->escapeHtml($this->getConfigAdmin()->getRecipientBankAccountNumber());
	}

	/** @return string */
	public function getRecipientBankId() {
		return $this->escapeHtml($this->getConfigAdmin()->getRecipientBankId());
	}

	/** @return string */
	public function getRecipientBankLoro() {
		return $this->escapeHtml($this->getConfigAdmin()->getRecipientBankLoro());
	}

	/** @return string */
	public function getRecipientBankName() {
		return $this->escapeHtml($this->getConfigAdmin()->getRecipientBankName());
	}

	/** @return string */
	public function getRecipientName() {
		return $this->escapeHtml($this->getConfigAdmin()->getRecipientName());
	}

	/** @return string */
	public function getRecipientTaxNumber() {
		return $this->escapeHtml($this->getConfigAdmin()->getRecipientTaxNumber());
	}

	/** @return string */
	public function getPaymentPurpose() {
		return
			$this->escapeHtml(
				strtr(
					$this->getConfigAdmin()->getPaymentPurposeTemplate()
					,array(
						self::PAYMENT_PURPOSE_TEMPLATE__PARAM__ORDER_ID =>
							$this->getOrder()->getDataUsingMethod(
								Df_Sales_Const::ORDER_PARAM__INCREMENT_ID
							)
						,self::PAYMENT_PURPOSE_TEMPLATE__PARAM__ORDER_DATE =>
							df_dts(
								$this->getOrderDate()
								, Df_Core_Model_Format_Date::FORMAT__RUSSIAN
							)
					)
				)
			)
		;
	}

	/** @return int */
	public function getOrderYear() {return rm_int(df_dts($this->getOrderDate(), Zend_Date::YEAR));}

	/** @return Df_Pd4_Model_Config_Area_Admin */
	protected function getConfigAdmin() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getActionDf()->getPaymentMethod()->getRmConfig()->admin();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getDefaultTemplate() {return 'df/pd4/document/rows.phtml';}
	/** @return Df_Pd4_Model_Request_Document_View */
	private function getActionDf() {return df_h()->pd4()->getDocumentViewAction();}
	/** @return Df_Sales_Model_Order */
	private function getOrder() {return $this->getActionDf()->getOrder();}
	/** @return Mage_Sales_Model_Order_Address */
	private function getCustomerAddress() {return $this->getOrder()->getBillingAddress();}
	/** @return Zend_Date */
	private function getOrderDate() {return $this->getOrder()->getDateCreated();}

	const _CLASS = __CLASS__;
	const PAYMENT_PURPOSE_TEMPLATE__PARAM__ORDER_ID = '{order.id}';
	const PAYMENT_PURPOSE_TEMPLATE__PARAM__ORDER_DATE = '{order.date}';

	/** @return Df_Pd4_Block_Document_Rows */
	public static function i() {return df_block(__CLASS__);}
}