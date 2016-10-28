<?php
namespace Df\Pd4\Block;
abstract class LinkToDocument extends \Df_Core_Block_Template_NoCache {
	/**
	 * @abstract
	 * @used-by getPaymentDocumentUrl()
	 * @used-by needToShow()
	 * @return \Df_Sales_Model_Order
	 */
	abstract protected function order();

	/**
	 * @override
	 * @return string
	 */
	public function getArea() {return \Df_Core_Const_Design_Area::FRONTEND;}

	/** @return string */
	public function getPaymentDocumentUrl() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getUrl('df-pd4', array(
				self::URL_PARAM__ORDER_PROTECT_CODE => $this->order()->getProtectCode()
			));
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	protected function needToShow() {
		return
				parent::needToShow()
			&&
				$this->order()->getPayment()
			&&
				$this->order()->getPayment()->getMethodInstance() instanceof \Df\Pd4\Method
		;
	}

	const URL_PARAM__ORDER_PROTECT_CODE = 'order';
}