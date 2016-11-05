<?php
class Df_Reports_Model_Settings_Common extends Df_Core_Model_Settings {
	/** @return boolean */
	public function enableGroupByWeek() {return $this->getYesNo('enable_group_by_week');}
	/** @return boolean */
	public function getEnabled() {return $this->getYesNo('enabled');}
	/** @return string */
	public function getPeriodDuration() {return $this->v('period_duration');}
	/** @return boolean */
	public function needRemoveTimezoneNotice() {return $this->getYesNo('remove_timozone_notice');}
	/** @return boolean */
	public function needSetEndDateToTheYesterday() {
		return $this->getYesNo('set_end_date_to_the_yesterday');
	}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_reports/common/';}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}