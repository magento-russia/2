<?php
namespace Df\YandexMarket\Settings;
class Diagnostics extends Yml {
	/** @return boolean */
	public function isEnabled() {return $this->getYesNo('enabled');}
	/** @return int */
	public function getLimit() {return $this->nat('limit');}
	/** @return boolean */
	public function needExplainRejection() {return $this->getYesNo('need_explain_rejection');}
	/** @return boolean */
	public function needLimit() {return $this->getYesNo('need_limit');}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_yandex_market/diagnostics/';}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}