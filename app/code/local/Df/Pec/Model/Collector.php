<?php
class Df_Pec_Model_Collector extends Df_Shipping_Model_Collector {
	/**
	 * @override
	 * @return Df_Pec_Model_Method[]
	 */
	protected function getMethods() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * 2016-11-10
			 * Раньше эта проверка выполнялась в методе
			 * @see Df_Pec_Model_Api_Calculator::getLocationDestinationId():
			 * df_assert($this->getRequest()->getDestinationCity(), 'Укажите город.');
			 * Это было неправильно, потому что возбуждаемая исключительная ситуация является обобщённой,
			 * и система её логирует и отсылает по почте:
			 * При добавлении товара в корзину «ПЭК» создаёт диагностический отчёт
			 * с сообщением «Укажите город»: http://magento-forum.ru/topic/5495/
			 * Вместо обобщённой исключительной ситуции здесь я теперь возбуждаю специализированную.
			 */
			$this->checkCityDest();
			/** @var Df_Pec_Model_Method[] $result */
			$result = array();
			/** @var array(string => array(string, string)) $methods */
			$methods = array(
				Df_Pec_Model_Method_Air::METHOD => array(Df_Pec_Model_Method_Air::_CLASS, 'Воздушный')
				,Df_Pec_Model_Method_Ground::METHOD => array(Df_Pec_Model_Method_Ground::_CLASS, 'Наземный')
			);
			foreach ($methods as $methodId => $methodData) {
				/** @var array(string, string) $methodData */
				/** @var string $methodClass */
				$methodClass = rm_first($methodData);
				df_assert_string($methodClass);
				/** @var string $methodTitle */
				$methodTitle = rm_last($methodData);
				df_assert_string($methodTitle);
				/** @var string $methodId */
				df_assert_string($methodId);
				/** @var array(string => int|float)|null $rate */
				$rate = df_a($this->getApi()->getRates(), $methodId);
				if (!is_null($rate)) {
					df_assert_array($rate);
					/** @var Df_Pec_Model_Method $method */
					$method = $this->createMethod($methodClass, $methodTitle);
					df_assert($method instanceof Df_Pec_Model_Method);
					// Обратите внимание, что информация о сроках доставки может отсутствовать.
					$method
						->setTimeOfDeliveryMax(
							df_a($rate, Df_Pec_Model_Api_Calculator::RESULT__DELIVERY_TIME_MAX, 0)
						)
						->setTimeOfDeliveryMin(
							df_a($rate, Df_Pec_Model_Api_Calculator::RESULT__DELIVERY_TIME_MIN, 0)
						)
						->setCost(
							$method->convertFromRoublesToBase(
								df_a($rate, Df_Pec_Model_Api_Calculator::RESULT__RATE)
							)
						)
					;
					$result[]= $method;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Pec_Model_Api_Calculator */
	private function getApi() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Pec_Model_Api_Calculator::i(array(
				Df_Pec_Model_Api_Calculator::P__REQUEST => $this->getRateRequest()
				,Df_Pec_Model_Api_Calculator::P__RM_CONFIG => $this->getRmConfig()
			));
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
}