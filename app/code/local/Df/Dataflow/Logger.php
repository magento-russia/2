<?php
interface Df_Dataflow_Logger {
	/**
	 * @param string|array(string|int => string) $message
	 * @return Df_1C_Helper_Data
	 */
	public function log($message);

	/**
	 * Раньше вместо 'Df_Dataflow_Logger' использовалось Df_Dataflow_Logger::_CLASS,
	 * однако это привело к сбою:
	 * «%s»
	 * Fatal error: Cannot inherit previously-inherited or override constant _CLASS
	 * from interface Df_Dataflow_Logger in app/code/local/Df/1C/Helper/Data.php on line 2
	 */
}