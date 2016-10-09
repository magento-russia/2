<?php
class Df_YandexMarket_Model_Settings_Diagnostics extends Df_YandexMarket_Model_Settings_Yml {
	/** @return boolean */
	public function isEnabled() {return $this->getYesNo('enabled');}
	/** @return int */
	public function limit() {return
		$this->isEnabled() && $this->needLimit() ? $this->getNatural('limit') : 0
	;}
	/** @return boolean */
	public function needExplainRejection() {return $this->getYesNo('need_explain_rejection');}
	/** @return boolean */
	public function needLimit() {return $this->getYesNo('need_limit');}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_yandex_market/diagnostics/';}
	/** @return Df_YandexMarket_Model_Settings_Diagnostics */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}