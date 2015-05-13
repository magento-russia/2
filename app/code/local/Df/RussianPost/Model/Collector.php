<?php
class Df_RussianPost_Model_Collector extends Df_Shipping_Model_Collector {
	/**
	 * @override
	 * @return Df_Shipping_Model_Method[]
	 * @throws Exception
	 */
	protected function getMethods() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Shipping_Model_Method[] $result */
			$result = array();
			if (
					$this->getRateRequest()->getOriginCountry()->isRussia()
				&&
					$this->getRateRequest()->getDestinationCountry()->isRussia()
				&&
					(31.5 >= $this->getRateRequest()->getWeightInKilogrammes())
				&&
					$this->getRateRequest()->getOriginPostalCode()
				&&
					$this->getRateRequest()->getDestinationPostalCode()
			) {
				foreach ($this->getDomesticApi()->getRatesAsText() as $textualRate) {
					/** @var string $textualRate */
					df_assert_string($textualRate);
					$method = $this->createMethodByTextualRate($textualRate);
					$result[]= $method;
				}
			}
			try {
				/** @var Df_RussianPost_Model_Official_Method_International $methodInternational */
				$methodInternational =
					$this->createMethod(
						$class = Df_RussianPost_Model_Official_Method_International::_CLASS
						,$title = 'Ценная посылка'
					)
				;
				if (
						$methodInternational->isApplicable()
					&&
						(0 < $methodInternational->getCost())
				) {
					$result[]= $methodInternational;
				}
			}
			catch(Exception $e) {
				if (!($e instanceof Df_Core_Exception_Client)) {
					df_notify_exception($e);
				}
				if (!$result && $this->getRmConfig()->frontend()->needDisplayDiagnosticMessages()) {
					throw $e;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $textualRate
	 * @return Df_Shipping_Model_Method
	 */
	private function createMethodByTextualRate($textualRate) {
		/** @var Df_Shipping_Model_Method $result */
		$result = null;
		/** @var string $methodClass */
		$methodClass = null;
		/** @var string $methodTitle */
		$methodTitle = null;
		/** @var int $titleLength */
		$titleLength = 0;
		foreach ($this->getCarrier()->getAllowedMethodsAsArray() as $methodData) {
			/** @var array $methodData */
			df_assert_array($methodData);
			/** @var string $title */
			$title = df_a($methodData, 'title');
			df_assert_string($title);
			if (rm_starts_with($textualRate, $title)) {
				/** @var int $currentTitleLength */
				$currentTitleLength = mb_strlen($title);
				if ($currentTitleLength > $titleLength) {
					$methodClass = df_a($methodData, 'class');
					$methodTitle = $title;
				}
			}
		}
		df_assert_string($methodClass);
		df_assert_string($methodTitle);
		/** @var Df_RussianPost_Model_RussianPostCalc_Method $result */
		$result = $this->createMethod($methodClass, $methodTitle);
		df_assert($result instanceof Df_RussianPost_Model_RussianPostCalc_Method);
		$result->setRateAsText($textualRate);
		return $result;
	}

	/** @return Df_RussianPost_Model_RussianPostCalc_Api */
	private function getDomesticApi() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_RussianPost_Model_RussianPostCalc_Api::i(array(
				Df_RussianPost_Model_RussianPostCalc_Api::P__WEIGHT =>
					$this->getRateRequest()->getWeightInKilogrammes()
				,Df_RussianPost_Model_RussianPostCalc_Api::P__DECLARED_VALUE =>
					rm_currency()->convertFromBaseToRoubles($this->declaredValueBase())
				,Df_RussianPost_Model_RussianPostCalc_Api::P__SOURCE__POSTAL_CODE =>
					$this->getRateRequest()->getOriginPostalCode()
				,Df_RussianPost_Model_RussianPostCalc_Api::P__DESTINATION__POSTAL_CODE =>
					$this->getRateRequest()->getDestinationPostalCode()
			));
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
}