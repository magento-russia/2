<?php
namespace Df\Pd4\Request\Document;
use Df\Pd4\Method as Method;
use Df_Sales_Model_Order as O;
use Df_Sales_Model_Resource_Order as RO;
use Mage_Sales_Model_Order_Payment as OP;
class View extends \Df_Core_Model {
	/**
	 * @used-by \Df\Pd4\Block\Document\Rows::order()
	 * @return O
	 */
	public function order() {
		if (!isset($this->{__METHOD__})) {
			/** @var O $result */
			$result = O::i();
			$result->load($this->getOrderId());
			if (df_nat0($result->getId()) !== df_nat0($this->getOrderId())) {
				df_error('Заказ №%d отсутствует в системе.', $this->getOrderId());
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by \Df\Pd4\Block\Document\Rows::getOrderAmountFractionalPartAsString()
	 * @used-by \Df\Pd4\Block\Document\Rows::getOrderAmountIntegerPartAsString()
	 * @return \Df_Core_Model_Money
	 */
	public function amount() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->configS()->getOrderAmountInServiceCurrency(
				$this->order()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return Method */
	public function method() {
		if (!isset($this->{__METHOD__})) {
			/** @var Method $result */
			$result = null;
			/**
			 * Раньше здесь стояло if(!is_null($this->order()->getPayment()))
			 * Как ни странно, иногда $this->order()->getPayment() возвращает и не null,
			 * и не объект.
			 */
			if ($this->order()->getPayment() instanceof OP) {
				$result = $this->order()->getPayment()->getMethodInstance();
			}
			if (!$result instanceof Method) {
				df_error(
					"Заказ №{$this->getOrderId()} не предназначен для оплаты через банковскую кассу."
				);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	private function getOrderId() {
		if (!isset($this->{__METHOD__})) {
			/** @var int $result */
			$result = null;
			try {
				$result = RO::s()->getOrderIdByProtectCode($this->getOrderProtectCode());
			}
			catch (\Exception $e) {
				df_error('Заказ с кодом «%s» отсутствует в системе.', $this->getOrderProtectCode());
			}
			try {
				$result = df_nat($result);
			}
			catch (\Exception $e) {
				df_error($this->getInvalidUrlMessage());
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return integer */
	private function getOrderProtectCode() {
		/** @var integer $result */
		$result = df_request(\Df\Pd4\Block\LinkToDocument::URL_PARAM__ORDER_PROTECT_CODE);
		df_assert(!is_null($result), $this->getInvalidUrlMessage());
		return $result;
	}

	/** @return string */
	private function getInvalidUrlMessage() {
		return df_t()->nl2br(
			"Вероятно, Вы хотели распечатать квитанцию?"
			."\nОднако ссылка на квитанцию не совсем верна."
			."\nМожет быть, Вы не полностью скопировали ссылку в адресную строку браузера?"
			."\nПопробуйте аккуратно ещё раз."
			."\nЕсли Вы вновь увидите данное сообщение — обратитесь к администратору магазина,"
			." приложив к вашему обращению ссылку на квитанцию."
		);
	}

	/** @return \Df\Payment\Config\Area\Service */
	private function configS() {return $this->method()->configS();}

	/** @return self */
	public static function i() {return new self;}
}