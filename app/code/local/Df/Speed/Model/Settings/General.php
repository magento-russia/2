<?php
class Df_Speed_Model_Settings_General extends Df_Core_Model_Settings {
	/** @return boolean */
	public function disableLoggingLastVisitTime() {return $this->getYesNo('disable_logging_last_visit_time');}
	/** @return boolean */
	public function disableVisitorLogging() {return $this->getYesNo('disable_visitor_logging');}
	/** @return boolean */
	public function enablePhpScriptsLoadChecking() {
		return $this->getYesNo('enable_php_scripts_load_checking');
	}
	/** @return boolean */
	public function enableZendDateCaching() {return $this->getYesNo('enable_zend_date_caching');}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_speed/general/';}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}