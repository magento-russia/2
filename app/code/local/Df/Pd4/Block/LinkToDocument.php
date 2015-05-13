<?php
abstract class Df_Pd4_Block_LinkToDocument extends Df_Core_Block_Template_NoCache {
	/**
	 * @override
	 * @return string
	 */
	public function getArea() {
		return Df_Core_Const_Design_Area::FRONTEND;
	}

	/** @return string */
	public function getPaymentDocumentUrl() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->getUrl(
					Df_Pd4_Const::PAYMENT_DOCUMENT_URL_BASE
					,array(
						Df_Pd4_Const::URL_PARAM__ORDER_PROTECT_CODE =>
							$this->getOrder()->getData(Df_Sales_Const::ORDER_PARAM__PROTECT_CODE)
					)
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	protected function needToShow() {
		return
				parent::needToShow()
			&&
				$this->getOrder()
			&&
				$this->getOrder()->getPayment()
			&&
				($this->getOrder()->getPayment()->getMethodInstance() instanceof Df_Pd4_Model_Payment)
		;
	}

	/**
	 * @abstract
	 * @return Mage_Sales_Model_Order
	 */
	abstract protected function getOrder();

	const _CLASS = __CLASS__;
}