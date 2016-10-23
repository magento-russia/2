<?php
/**
 * 2015-03-15
 * Обратите внимание, что данный класс не имеет какого-либо внешнего интерфейса
 * (не имеет ни одного публичного метода), и его единственная цель —
 * предоставлять общие методы своим потомкам.
 */
abstract class Df_Payment_Request extends Df_Core_Model {
	/**
	 * @used-by amount()
	 * @used-by getPayment()
	 * @return Df_Sales_Model_Order
	 */
	abstract protected function order();

	/**
	 * 2015-03-09
	 * Обратите внимание, что 2 платёжных модуля (IPay и Kkb)
	 * повышают видимость данного метода до публичной:
	 * @used-by Df_IPay_Request_Payment::getAmount()
	 * @used-by Df_Kkb_Request_Payment::getAmount()
	 * @used-by Df_Kkb_Request_Secondary::getAmount()
	 * Намеренно не делаем данный метод публичным в базовом классе
	 * ради упрощения понимания системы
	 * (чем меньше публичных методов — тем проще понимать систему).
	 * @return Df_Core_Model_Money
	 */
	protected function amount() {return dfc($this, function() {return
		$this->configS()->getOrderAmountInServiceCurrency($this->order())
	;});}

	/** @return string */
	protected function amountF() {return $this->amount()->getOriginalAsFloat();}

	/** @return string */
	protected function amountFF() {return $this->amount()->getAsFixedFloat();}

	/** @return string */
	protected function amountS() {return $this->amount()->getAsString();}

	/** @return Df_Payment_Config_Area_Service */
	protected function configS() {return $this->method()->configS();}

	/** @return string */
	protected function description() {return dfc($this, function() {return
		strtr($this->configS()->description(), ['{order.id}' => $this->orderIId()])
	;});}

	/**
	 * @used-by Df_Alfabank_Request_Payment::getResponseAsArray()
	 * @used-by Df_Psbank_Request_Secondary::getResponsePayment()
	 * @return Mage_Payment_Model_Info
	 */
	protected function ii() {return $this->method()->getInfoInstance();}

	/**
	 * @override
	 * @used-by configS()
	 * @used-by ii()
	 * @return Df_Payment_Method_WithRedirect
	 */
	protected function method() {return dfc($this, function() {
		/** @var Mage_Sales_Model_Order_Payment $result */
		/** @var Df_Payment_Method_WithRedirect $result */
		$result = $this->payment()->getMethodInstance();
		if (!$result instanceof Df_Payment_Method_WithRedirect) {
			df_error(
				'Заказ №«%s» не предназначен для оплаты каким-либо из платёжных модулей
				Российской сборки Magento.'
				,$this->order()->getIncrementId()
			);
		}
		return $result;
	});}

	/**
	 * 2015-02-19
	 * Этот метод аналогичен @see orderIId(),
	 * но возвращает короткий (целочисленный) идентификатор заказа.
	 * Как правило, администратору интернет-магазина (и покупателю)
	 * удобнее работать с длинным (символьным) идентификатором заказа,
	 * и именно длинный (символьный) идентификатор заказа отображается
	 * администратору и покупателю в стандартном интерфейсе Magento в стандартных сценариях
	 * (покупателю, в частности, после успешной оплаты заказа, в личном кабинете,
	 * в относящихся к заказу письмах-оповещениях).
	 * Поэтому почти все платёжные модули Российской сборки Magento
	 * используют именно @see orderIId(), а не @see orderId().
	 * Однако некоторые платёжные системы накладывают ограничения на длину идентификатора запроса,
	 * которым @see orderIId() может не удовлетворять. И вот тогда используется @see orderId().
	 * @used-by Df_IPay_Request_Payment::_params()
	 * @used-by STUB::_params()
	 * @used-by STUB::_params()
	 * @return string
	 */
	protected function orderId() {return $this->order()->getId();}

	/**
	 * 2015-03-19
	 * Фундаментальный метод для потомков:
	 * при текущей архитектуре именно идентификатор заказа
	 * идентифицирует запрос интернет-магазина к платёжной системе.
	 * Вообще говоря, это не совсем правильно:
	 * в общем случае ведь одному заказу может соответствовать ведь несколько попыток оплаты
	 * (или, скажем, оплата частями).
	 * По-правильному, видимо, надо переделать архитектуру так,
	 * чтобы запрос интернет-магазина к платёжной системе идентифицировался не заказом,
	 * а какой-нибудь из сущностей Magento, соответсвующих платежу:
	 * возможно, @see Mage_Sales_Model_Order_Payment
	 * @used-by description()
	 * @used-by Df_Alfabank_Request_Payment::_params()
	 * @used-by Df_Assist_Request_Payment::_params()
	 * @used-by Df_Avangard_Request_Payment::getRequestDocument()
	 * @used-by Df_EasyPay_Request_Payment::_params()
	 * @used-by Df_EasyPay_Request_Payment::getSignature()
	 * @used-by Df_Interkassa_Request_Payment::_params()
	 * @used-by Df_Kkb_Request_Payment::orderIId()
	 * @used-by Df_LiqPay_Request_Payment::getParamsForXml()
	 * @used-by Df_Moneta_Request_Payment::_params()
	 * @used-by Df_Moneta_Request_Payment::getSignature()
	 * @used-by Df_OnPay_Request_Payment::_params()
	 * @used-by Df_OnPay_Request_Payment::getSignature()
	 * @used-by Df_PayOnline_Request_Payment::_params()
	 * @used-by Df_PayOnline_Request_Payment::getSignature()
	 * @used-by Df_Psbank_Request_Payment::getParamsForSignature()
	 * @used-by Df_Qiwi_Request_Payment::_params()
	 * @used-by Df_RbkMoney_Request_Payment::_params()
	 * @used-by Df_Robokassa_Request_Payment::_params()
	 * @used-by Df_Robokassa_Request_Payment::getSignature()
	 * @used-by Df_Uniteller_Request_Payment::_params()
	 * @used-by Df_Uniteller_Request_Payment::getSignature()
	 * @used-by Df_WalletOne_Request_Payment::paramsCommon()
	 * @used-by Df_WebMoney_Request_Payment::orderIId()
	 * @used-by Df_WebPay_Request_Payment::_params()
	 * @used-by Df_WebPay_Request_Payment::getSignature()
	 * @return string
	 */
	protected function orderIId() {return $this->order()->getIncrementId();}

	/**
	 * 2015-03-15
	 * Потомки используют этот криптографический ключ
	 * для шифрования своих обращений к платёжной системе
	 * @return string
	 */
	protected function password() {return $this->configS()->getRequestPassword();}

	/**
	 * 2015-03-08
	 * Обратите внимание, что:
	 * 1) @uses Mage_Sales_Model_Order::getPayment() может иногда возвращать false
	 * 2) Результат @uses Mage_Sales_Model_Order::getPayment() разумно кэшировать
	 * в силу реализации этого метода (там используется foreach).
	 * @see Df_Payment_Action_Abstract::getPayment()
	 * @used-by method()
	 * @return Mage_Sales_Model_Order_Payment
	 */
	protected function payment() {return dfc($this, function() {
		/** @var Mage_Sales_Model_Order_Payment $result */
		$result = $this->order()->getPayment();
		df_assert($result instanceof Mage_Sales_Model_Order_Payment);
		return $result;
	});}

	/**
	 * 2015-03-15
	 * Идентификатор интернет-магазина при обращении интернет-магазина к платёжной системе.
	 * В силу того, что при обращении интернет-магазина к платёжной системе
	 * интернет-магазину всегда нужно себя идентифицировать, этот метод нужен всем потомкам.
	 * @return string
	 */
	protected function shopId() {return $this->configS()->getShopId();}

	/**
	 * @used-by Df_YandexMoney_Request_Payment::descriptionParams()
	 * @return Df_Core_Model_StoreM
	 */
	protected function store() {return df_store($this->method()->getStore());}
}