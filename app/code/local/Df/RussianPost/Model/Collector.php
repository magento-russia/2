<?php
class Df_RussianPost_Model_Collector extends Df_Shipping_Collector {
	/**
	 * @override
	 * @return Df_Shipping_Model_Method[]
	 */
	protected function getMethods() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Shipping_Model_Method[] $result */
			$result = array();
			foreach ($this->getDomesticApi()->getRatesAsText() as $textualRate) {
				/** @var string $textualRate */
				df_assert_string($textualRate);
				$result[]= $this->createDomesticMethod($textualRate);
			}
			$result[]= $this->createMethod(
				Df_RussianPost_Model_Official_Method_International::_C, 'Ценная посылка'
			);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $textualRate
	 * @return Df_Shipping_Model_Method
	 */
	private function createDomesticMethod($textualRate) {
		/** @var Df_Shipping_Model_Method $result */
		$result = null;
		/** @var string $methodClass */
		$methodClass = null;
		/** @var string $methodTitle */
		$methodTitle = null;
		/** @var int $titleLength */
		$titleLength = 0;
		foreach ($this->main()->getAllowedMethodsAsArray() as $methodData) {
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
		$result->setRateT($textualRate);
		return $result;
	}

	/** @return Df_RussianPost_Model_RussianPostCalc_Api */
	private function getDomesticApi() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_RussianPost_Model_RussianPostCalc_Api::i(
				$this->getRateRequest()->getOriginPostalCode()
				, $this->getRateRequest()->getDestinationPostalCode()
				, $this->getRateRequest()->getWeightInKilogrammes()
				, rm_currency_h()->convertFromBaseToRoubles($this->declaredValueBase())
			);
		}
		return $this->{__METHOD__};
	}
}