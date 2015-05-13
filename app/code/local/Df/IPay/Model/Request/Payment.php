<?php
/**
 * @method Df_IPay_Model_Payment getPaymentMethod()
 * @method Df_IPay_Model_Config_Area_Service getServiceConfig()
 */
class Df_IPay_Model_Request_Payment extends Df_Payment_Model_Request_Payment {
	/**
	 * @override
	 * @return array
	 */
	protected function getParamsInternal() {
		/** @var array $result */
		$result =
			array_merge(
				array(
					self::REQUEST_VAR__SHOP_ID => $this->getServiceConfig()->getShopId()
					,self::REQUEST_VAR__ORDER_NUMBER =>
						$this
							->getOrder()
							/**
							 * iPay допускает не больше 6 символов в номере платежа,
							 * поэтому используем getId вместо обычного getIncrementId
							 */
							->getId()
					,self::REQUEST_VAR__ORDER_AMOUNT =>
						$this
							->getAmount()
							/**
							 * iPay требует, чтобы суммы были целыми числами
							 */
							->getAsInteger()
					,'amount_editable' => 'N'
					,self::REQUEST_VAR__URL_RETURN =>
						/**
						 * iPay (как и LiqPay),
						 * в отличие от других платёжных систем,
						 * не поддерживает разные веб-адреса
						 * для успешного и неуспешного сценариев оплаты
						 */
						$this->getUrlReturn()
				)
			)
		;
		return $result;
	}

	/** @return string */
	private function getUrlReturn() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Mage::getUrl(
				df_concat_url($this->getPaymentMethod()->getCode(), 'customerReturn'						)
				// Без _nosid система будет формировать ссылку c ?___SID=U.
				// На всякий случай избегаем этого.
				,array('_nosid' => true)
			);
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
	const REQUEST_VAR__ORDER_NUMBER = 'pers_acc';
	const REQUEST_VAR__ORDER_AMOUNT = 'amount';
	const REQUEST_VAR__SHOP_ID = 'srv_no';
	const REQUEST_VAR__URL_RETURN = 'provider_url';
	/**
	 * @static
	 * @param Df_Sales_Model_Order $order
	 * @return Df_IPay_Model_Request_Payment
	 */
	public static function i(Df_Sales_Model_Order $order) {
		return new self(array(self::P__ORDER => $order));
	}
}