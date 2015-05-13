<?php
class Df_Shipping_Model_Config_Area_Admin extends Df_Shipping_Model_Config_Area_Abstract {
	/** @return float */
	public function feeFixed() {return rm_float($this->getVar('fee_fixed', 0.0));}

	/** @return float */
	public function feePercent() {return rm_float($this->getVar('fee_percent', 0.0));}

	/** @return float */
	public function getDeclaredValuePercent() {
		return rm_float($this->getVar('declared_value_percent', 0.0));
	}

	/** @return int */
	public function getProcessingBeforeShippingDays() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $prefix */
			$prefix = 'processing_before_shipping__';
			/** @var int $result */
			$result = rm_nat0($this->getVar($prefix . 'days', 0));
			/** @var int $canShipTodayUntill */
			$canShipTodayUntill = rm_nat0($this->getVar($prefix . 'consider_today_untill', 12));
			/** @var bool $canShipToday */
			$canShipToday = df()->date()->getHour() < $canShipTodayUntill;
			if (!$canShipToday) {
				$result++;
			}
			if ($this->getVarFlag($prefix . 'consider_days_off', true)) {
				$result = df()->date()->getNumCalendarDaysByNumWorkingDays(
					$startDate = $canShipToday ? Zend_Date::now() : df()->date()->tomorrow()
					,$numWorkingDays = $result
					,$store = $this->getStore()
				);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getAreaPrefix() {return 'admin';}

	const _CLASS = __CLASS__;
}