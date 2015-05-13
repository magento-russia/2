<?php
abstract class Df_Core_Model_Settings_Jquery extends Df_Core_Model_Settings {
	/**
	 * @abstract
	 * @return string
	 */
	abstract public function getLoadMode();
	/**
	 * @abstract
	 * @return boolean
	 */
	abstract public function needRemoveExtraneous();
}