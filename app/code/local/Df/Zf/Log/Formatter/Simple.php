<?php
class Df_Zf_Log_Formatter_Simple extends Zend_Log_Formatter_Simple {
	/**
	 * @override
	 * @param string|null $format [optional]
	 * @throws Zend_Log_Exception
	 */
	public function __construct($format = null) {
		if (is_null($format)) {
			$format = '%timestamp%: %message%' . PHP_EOL;
		}
		parent::__construct($format);
	}
}