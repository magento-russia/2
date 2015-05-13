<?php
class Df_Dellin_Model_Method extends Df_Shipping_Model_Method {
	/**
	 * @override
	 * @return float
	 */
	public function getCost() {
		if (!isset($this->{__METHOD__})) {
			if (0 === $this->getCostInRoubles()) {
				$this->throwExceptionCalculateFailure();
			}
			$this->{__METHOD__} = $this->convertFromRoublesToBase($this->getCostInRoubles());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	public function getMethod() {return __CLASS__;}

	/**
	 * @override
	 * @return string
	 */
	public function getMethodTitle() {
		/** @var string $result */
		$result = '';
		if (!is_null($this->getRequest()) && (0 !== $this->getTimeOfDelivery())) {
			$result = rm_sprintf('%s', $this->formatTimeOfDelivery($this->getTimeOfDelivery()));
		}
		return $result;
	}

	/**
	 * @override
	 * @return bool
	 * @throws Exception
	 */
	public function isApplicable() {
		/** @var bool $result */
		$result = parent::isApplicable();
		if ($result) {
			try {
				$this
					->checkCountryOriginIsRussia()
					->checkCountryDestinationIsRussia()
					->checkCityOriginIsNotEmpty()
					->checkCityDestinationIsNotEmpty()
					->checkOriginAndDestinationCitiesAreDifferent()
				;
				if (!$this->getLocationIdOrigin()) {
					$this->throwExceptionInvalidOrigin();
				}
				if (!$this->getLocationIdDestination()) {
					$this->throwExceptionInvalidDestination();
				}
			}
			catch(Exception $e) {
				if ($this->needDisplayDiagnosticMessages()) {throw $e;} else {$result = false;}
			}
		}
		return $result;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getLocationIdDestination() {
		return $this->getRequest()->getLocatorDestination()->getResult();
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getLocationIdOrigin() {
		return $this->getRequest()->getLocatorOrigin()->getResult();
	}

	/** @return Df_Dellin_Model_Request_Rate */
	private function getApi() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Dellin_Model_Request_Rate::i(array(
				'derivalPoint' => $this->getLocationIdOrigin()
				,'arrivalPoint' => $this->getLocationIdDestination()
				,'sizedWeight' => $this->getRequest()->getWeightInKilogrammes()
				,'sizedVolume' => $this->getRequest()->getVolumeInCubicMetres()
				,'statedValue' => $this->getRequest()->getDeclaredValueInRoubles()
				,'packages' => '0x838FC70BAEB49B564426B45B1D216C15'
			));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return int
	 */
	private function getCostInRoubles() {
		/**
		 * Обратите внимание, что служба доставки «Деловые Линии»
		 * на самом деле возвращает стоимость доставки в виде дробного числа, с копейками,
		 * например: «737.5».
		 * @link http://magento-forum.ru/topic/4476/
		 */
		return intval($this->getApi()->getRate());
	}

	/** @return int */
	private function getTimeOfDelivery() {
		/** @var int $result */
		$result = 0;
		try {
			$result = rm_nat($this->getApi()->getDeliveryTimeInDays());
		}
		catch(Exception $e) {
			/**
			 * Вот здесь вываливать исключительную ситуацию наружу нам не нужно,
			 * потому что метод getTimeOfDelivery вызывается из метода getMethodTitle,
			 * а тот, в свою очередь, из ядра Magento,
			 * и там исключительную ситуаацию никто не обработает.
			 */
			Mage::logException($e);
		}
		df_result_integer($result);
		return $result;
	}

	const _CLASS = __CLASS__;
}