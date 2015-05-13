<?php
/**
 * @method Df_YandexMoney_Model_Config_Area_Service getServiceConfig()
 */
class Df_YandexMoney_Model_Request_Payment extends Df_Payment_Model_Request_Payment {
	/**
	 * Метод публичен, потому что его иногда использует сторонний класс:
	 * @see Df_YandexMoney_Model_Request_Authorize::getRequestParams()
	 * @return string
	 */
	public function getTransactionDescriptionForShop() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				!$this->getServiceConfig()->getTransactionDescriptionForShop()
				? $this->getTransactionDescription()
				: strtr(
					$this->getServiceConfig()->getTransactionDescriptionForShop()
					,$this->getTransactionDescriptionParams()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Метод публичен, потому что его иногда использует сторонний класс:
	 * @see Df_YandexMoney_Model_Request_Authorize::getRequestParams()
	 * @return string
	 */
	public function getTransactionTag() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				str_replace(
					array_keys($this->getTransactionDescriptionParams())
					,array_values($this->getTransactionDescriptionParams())
					,$this->getServiceConfig()->getTransactionTag()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getParamsInternal() {
		if (
				!$this->getServiceConfig()->isTestMode()
			&&
				(1 > $this->getAmount()->getAsFixedFloat())
		) {
			df_error(
				'В промышленном режиме минимальный платёж посредством Яндекс.Денег — 1 рубль.'
			);
		}
		return array(
			'client_id' => $this->getServiceConfig()->getAppId()
			,'response_type' => 'code'
			,'redirect_uri' => $this->getCustomerReturnUrl()
			,'scope' => $this->getScope()
		);
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getTransactionDescriptionParams() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array_merge(parent::getTransactionDescriptionParams(), array(
				'{website.domain}' => $this->getStoreUri()->getHost()
				,'{website.name}' => $this->getStore()->getWebsite()->getName()
				,'{website.code}' => $this->getStore()->getWebsite()->getCode()
				,'{store.name}' => $this->getStore()->getGroup()->getName()
				,'{storeView.name}' => $this->getStore()->getName()
				,'{storeView.code}' => $this->getStore()->getCode()
			));
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getScope() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = implode(' ', array(
				'money-source("wallet")'
				,strtr('payment.to-account("{номер счёта}").limit(,{сумма})', array(
					'{номер счёта}' => $this->getShopId()
					,'{сумма}' => $this->getAmount()->getAsString()
				))
			));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
	}

	/**
	 * @static
	 * @param Df_Sales_Model_Order $order
	 * @return Df_YandexMoney_Model_Request_Payment
	 */
	public static function i(Df_Sales_Model_Order $order) {
		return new self(array(self::P__ORDER => $order));
	}
}