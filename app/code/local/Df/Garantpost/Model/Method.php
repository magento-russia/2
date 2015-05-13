<?php
abstract class Df_Garantpost_Model_Method extends Df_Shipping_Model_Method {
	/**
	 * @abstract
	 * @return int
	 */
	abstract protected function getCostInRoubles();

	/**
	 * @override
	 * @return float
	 */
	public function getCost() {
		if (!isset($this->{__METHOD__})) {
			if (0 === $this->getCostInRoubles()) {
				df_notify_me('Вероятно, модуль Гарантпост перестал работать правильно.');
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
	public function getMethodTitle() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				!is_null($this->getRequest()) && (0 !== $this->getTimeOfDeliveryMin())
				? $this->formatTimeOfDelivery($this->getTimeOfDeliveryMin(), $this->getTimeOfDeliveryMax())
				: ''
			;
		}
		return $this->{__METHOD__};
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
				$this->checkCountryOriginIsRussia();
			}
			catch(Exception $e) {
				if ($this->needDisplayDiagnosticMessages()) {throw $e;} else {$result = false;}
			}
		}
		return $result;
	}

	/** @return int */
	protected function getTimeOfDeliveryMax() {return 0;}

	/** @return int */
	protected function getTimeOfDeliveryMin() {return 0;}

	/** @return bool */
	protected function isDeliveryFromMoscow() {return $this->getRequest()->isOriginMoscow();}

	const _CLASS = __CLASS__;
}