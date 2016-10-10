<?php
/**
 * @method Df_Kkb_Model_Config_Area_Service getServiceConfig()
 */
class Df_Kkb_Model_Request_Payment extends Df_Payment_Model_Request_Payment {
	/**
	 * Перекрываем родительский метод с целью обеспечить непустоту результатата.
	 * @override
	 * @return string
	 */
	protected function getCustomerEmail() {
		/** @var string|null $email */
		$email = parent::getCustomerEmail();
		return $email ? $email : 'admin@magento-forum.ru';
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getParamsInternal() {
		/** @var array(string => string) $result */
		$result =
			array(
				/**
				 * Из документации:
				 * «В данном поле передается XML документ закодированный в Base64
				 * (генерится автоматически, предоставляемой компонентой)»
				 *
				 * 2016-05-26
				 * https://testpay.kkb.kz/doc/htm/fields_description.html
				 */
				'Signed_Order_B64' => base64_encode($this->getDocumentRegistration()->getXml())
				/**
				 * Из документации: «e-mail покупателя»
				 *
				 * 2016-05-26
				 * «e-mail покупателя. Адрес должен быть реальным, иначе платеж может быть оклонен!»
				 * https://testpay.kkb.kz/doc/htm/fields_description.html
				 */
				,'email' =>  $this->getCustomerEmail()
				/**
				 * Из документации:
				 * «Линк для возврата покупателя в магазин (на сайт)
				 * после успешного проведения оплаты»
				 */
				,'BackLink' =>  $this->getUrlCheckoutSuccess()
				/**
				 * Из документации:
				 * «Линк для возврата покупателя в магазин (на сайт)
				 * после оплаты в случае неудачной авторизации»
				 */
				,'FailureBackLink' => $this->getUrlCheckoutFail()
				/**
				 * Из документации:
				 * «Линк для отправки результата авторизации в магазин.
				 * Результат авторизации представлен в виде расширенного XML документа»
				 */
				,'PostLink' => $this->getUrlConfirm()
				/**
				 * Из документации:
				 * «Линк для отправки неудачного результата авторизации
				 * либо информации об ошибке в магазин.»
				 */
				,'FailurePostLink' => $this->getUrlConfirm()
				/**
				 * Из документации:
				 * «В данном поле передается информация о языке интерфейса сервера авторизации»
				 */
				,'Language' => 'rus'
				/**
				 * Из документации:
				 * «В данном поле передается информация о товарах или услугах,
				 * за которые производится оплата. XML документ закодированный в Base64»
				 */
				,'appendix' => base64_encode($this->getDocumentOrderItems()->getXml())
			)
		;
//		rm_report(
//			'registration-{date}-{time}.xml', $this->getDocumentRegistration()->getXml()
//		);
//		rm_report(
//			'items-{date}-{time}.xml', $this->getDocumentOrderItems()->getXml()
//		);
		return $result;
	}
	
	/** @return Df_Kkb_Model_RequestDocument_OrderItems */
	private function getDocumentOrderItems() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Kkb_Model_RequestDocument_OrderItems::i($this);
		}
		return $this->{__METHOD__};
	}
	
	/** @return Df_Kkb_Model_RequestDocument_Registration */
	private function getDocumentRegistration() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Kkb_Model_RequestDocument_Registration::i($this);
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
}