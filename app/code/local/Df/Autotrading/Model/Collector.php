<?php
class Df_Autotrading_Model_Collector extends Df_Shipping_Collector {
	/**
	 * @override
	 * @return Df_Autotrading_Model_Method[]
	 * @throws Exception
	 */
	protected function getMethods() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Autotrading_Model_Method[] $result */
			$result = array();
			/** @var Df_Autotrading_Model_Method $method */
			$method = $this->createMethod(Df_Autotrading_Model_Method::_C, $title = 'Стандартный');
			/**
			 * Желательно вызвать isApplicable до вызова $this->getApi()->getRate(),
			 * чтобы не получить сбой на уровне калькулятора
			 */
			if ($method->isApplicable()) {
				/**
				 * Этот блок try... catch обязателен,
				 * иначе сбой не будет задокументирован,
				 * лишь покупатель увидит:
				 * «Если Вы хотите использовать этот способ доставки — оформите заказ по телефону».
				 */
				/** @var float $cost */
				/** @var int $deliveryTime */
				try {
					$cost = $this->getApi()->getRate();
					$deliveryTime = $this->getApi()->getDeliveryTime();
				}
				catch (Exception $e) {
					if (!$e instanceof Df_Core_Exception) {
						// документируем сбой
						df_notify_exception($e);
					}
					// передаём сбой дальше, чтобы покупатель увидел
					// «Если Вы хотите использовать этот способ доставки — оформите заказ по телефону».
					df_error($e);
				}
				if (0 < $cost) {
					$method->setCost($this->convertFromRoublesToBase($cost));
					$method->setTimeOfDelivery($deliveryTime);
					$result[]= $method;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Autotrading_Model_Request_Rate */
	private function getApi() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Autotrading_Model_Api_Calculator::api(
				$this->getRateRequest(), $this->config()
			);
		}
		return $this->{__METHOD__};
	}
}