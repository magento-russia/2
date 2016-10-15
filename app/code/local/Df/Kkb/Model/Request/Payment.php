<?php
/** @method Df_Kkb_Model_Config_Area_Service configS() */
class Df_Kkb_Model_Request_Payment extends Df_Payment_Model_Request_Payment {
	/**
	 * 2015-03-09
	 * Переопределяем метод с целью сделать его публичным конкретно для данного класса.
	 * @override
	 * @see Df_Payment_Model_Request::amount()
	 * @used-by Df_Kkb_Model_RequestDocument_Signed::amount()
	 * @see Df_Kkb_Model_Request_Secondary::amount()
	 * @return Df_Core_Model_Money
	 */
	public function amount() {return parent::amount();}

	/**
	 * 2015-03-09
	 * Переопределяем метод с целью сделать его публичным конкретно для данного класса.
	 * @override
	 * @see Df_Payment_Model_Request_Payment::orderIId()
	 * @used-by Df_Kkb_Model_RequestDocument_Signed::orderIId()
	 * @see Df_Kkb_Model_Request_Secondary::orderIId()
	 * @return string
	 */
	public function orderIId() {return parent::orderIId();}

	/**
	 * @override
	 * @see Df_Payment_Model_Request_Payment::_params()
	 * @used-by Df_Payment_Model_Request_Payment::params()
	 * @return array(string => string|int)
	 */
	protected function _params() {
		return array(
			// из документации:
			// «В данном поле передается XML документ закодированный в Base64
			// (генерится автоматически, предоставляемой компонентой)»
			//
			// 2016-05-26
			// https://testpay.kkb.kz/doc/htm/fields_description.html
			'Signed_Order_B64' => base64_encode($this->getDocumentRegistration()->getXml())
			// из документации: «e-mail покупателя»
			//
			// 2016-05-26
			// «e-mail покупателя. Адрес должен быть реальным, иначе платеж может быть оклонен!
			// https://testpay.kkb.kz/doc/htm/fields_description.html
			,'email' =>  $this->getCustomerEmail()
			// из документации:
			// «Линк для возврата покупателя в магазин (на сайт) после успешного проведения оплаты»
			,'BackLink' => df_url_checkout_success()
			// из документации:
			// «Линк для возврата покупателя в магазин (на сайт)
			// после оплаты в случае неудачной авторизации»
			,'FailureBackLink' => df_url_checkout_fail()
			// из документации:
			// «Линк для отправки результата авторизации в магазин.
			// Результат авторизации представлен в виде расширенного XML документа»
			,'PostLink' => $this->urlConfirm()
			// из документации:
			// «Линк для отправки неудачного результата авторизации
			// либо информации об ошибке в магазин.»
			,'FailurePostLink' => $this->urlConfirm()
			// из документации:
			// «В данном поле передается информация о языке интерфейса сервера авторизации»
			,'Language' => 'rus'
			// из документации:
			// «В данном поле передается информация о товарах или услугах,
			// за которые производится оплата. XML документ закодированный в Base64»
			,'appendix' => base64_encode($this->getDocumentOrderItems()->getXml())
		);
	}

	/**
	 * Перекрываем родительский метод с целью обеспечить непустоту результатата.
	 * @override
	 * @return string
	 */
	protected function getCustomerEmail() {
		return parent::email() ? parent::email() : 'admin@magento-forum.ru';
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

	/** @used-by Df_Kkb_Model_RequestDocument_OrderItems::_construct() */

}