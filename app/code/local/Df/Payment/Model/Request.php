<?php
/**
 * 2015-03-15
 * Обратите внимание, что данный класс не имеет какого-либо внешнего интерфейса
 * (не имеет ни одного публичного метода), и его единственная цель —
 * предоставлять общие методы своим потомкам.
 */
abstract class Df_Payment_Model_Request extends Df_Core_Model {
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
	 * @used-by Df_IPay_Model_Request_Payment::getAmount()
	 * @used-by Df_Kkb_Model_Request_Payment::getAmount()
	 * @used-by Df_Kkb_Model_Request_Secondary::getAmount()
	 * Намеренно не делаем данный метод публичным в базовом классе
	 * ради упрощения понимания системы
	 * (чем меньше публичных методов — тем проще понимать систему).
	 * @return Df_Core_Model_Money
	 */
	protected function amount() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->configS()->getOrderAmountInServiceCurrency($this->order());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	protected function amountF() {return $this->amount()->getOriginalAsFloat();}

	/** @return string */
	protected function amountFF() {return $this->amount()->getAsFixedFloat();}

	/** @return string */
	protected function amountS() {return $this->amount()->getAsString();}

	/** @return Df_Payment_Config_Area_Service */
	protected function configS() {return $this->method()->configS();}

	/**
	 * @used-by
	 * @return string
	 */
	protected function description() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = strtr($this->configS()->getTransactionDescription(), array(
				'{order.id}' => $this->orderIId()
			));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by Df_Alfabank_Model_Request_Payment::getResponseAsArray()
	 * @used-by Df_Psbank_Model_Request_Secondary::getResponsePayment()
	 * @return Mage_Payment_Model_Info
	 */
	protected function getInfoInstance() {return $this->method()->getInfoInstance();}

	/**
	 * @override
	 * @used-by configS()
	 * @used-by getInfoInstance()
	 * @return Df_Payment_Model_Method_WithRedirect
	 */
	protected function method() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Payment_Model_Method_WithRedirect $result */
			$result = $this->payment()->getMethodInstance();
			if (!$result instanceof Df_Payment_Model_Method_WithRedirect) {
				df_error(
					'Заказ №«%s» не предназначен для оплаты каким-либо из платёжных модулей
					Российской сборки Magento.'
					,$this->order()->getIncrementId()
				);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

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
	 * @used-by Df_IPay_Model_Request_Payment::_params()
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
	 * @used-by Df_Alfabank_Model_Request_Payment::_params()
	 * @used-by Df_Assist_Model_Request_Payment::_params()
	 * @used-by Df_Avangard_Model_Request_Payment::getRequestDocument()
	 * @used-by Df_EasyPay_Model_Request_Payment::_params()
	 * @used-by Df_EasyPay_Model_Request_Payment::getSignature()
	 * @used-by Df_Interkassa_Model_Request_Payment::_params()
	 * @used-by Df_Kkb_Model_Request_Payment::orderIId()
	 * @used-by Df_LiqPay_Model_Request_Payment::getParamsForXml()
	 * @used-by Df_Moneta_Model_Request_Payment::_params()
	 * @used-by Df_Moneta_Model_Request_Payment::getSignature()
	 * @used-by Df_OnPay_Model_Request_Payment::_params()
	 * @used-by Df_OnPay_Model_Request_Payment::getSignature()
	 * @used-by Df_PayOnline_Model_Request_Payment::_params()
	 * @used-by Df_PayOnline_Model_Request_Payment::getSignature()
	 * @used-by Df_Psbank_Model_Request_Payment::getParamsForSignature()
	 * @used-by Df_Qiwi_Model_Request_Payment::_params()
	 * @used-by Df_RbkMoney_Model_Request_Payment::_params()
	 * @used-by Df_Robokassa_Model_Request_Payment::_params()
	 * @used-by Df_Robokassa_Model_Request_Payment::getSignature()
	 * @used-by Df_Uniteller_Model_Request_Payment::_params()
	 * @used-by Df_Uniteller_Model_Request_Payment::getSignature()
	 * @used-by Df_WalletOne_Model_Request_Payment::paramsCommon()
	 * @used-by Df_WebMoney_Model_Request_Payment::orderIId()
	 * @used-by Df_WebPay_Model_Request_Payment::_params()
	 * @used-by Df_WebPay_Model_Request_Payment::getSignature()
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
	 * @see Df_Payment_Model_Action_Abstract::getPayment()
	 * @used-by method()
	 * @return Mage_Sales_Model_Order_Payment
	 */
	protected function payment() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->order()->getPayment();
			df_assert($this->{__METHOD__} instanceof Mage_Sales_Model_Order_Payment);
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-03-15
	 * Идентификатор интернет-магазина при обращении интернет-магазина к платёжной системе.
	 * В силу того, что при обращении интернет-магазина к платёжной системе
	 * интернет-магазину всегда нужно себя идентифицировать, этот метод нужен всем потомкам.
	 * @return string
	 */
	protected function shopId() {return $this->configS()->getShopId();}

	/**
	 * @used-by Df_YandexMoney_Model_Request_Payment::getTransactionDescriptionParams()
	 * @return Df_Core_Model_StoreM
	 */
	protected function store() {return rm_store($this->method()->getStore());}
}