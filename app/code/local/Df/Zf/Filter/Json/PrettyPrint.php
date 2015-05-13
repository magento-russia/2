<?php
class Df_Zf_Filter_Json_PrettyPrint implements Zend_Filter_Interface {
	/**
	 * @param string $value
	 * @return string
	 */
	public function filter($value) {
		df_param_string($value, 0);
		/** @var array $callable */
		$callable = array('Zend_Json', 'prettyPrint');
		/** @var string $result */
		$result =
				/**
				 * Раньше тут стояло method_exists.
				 * Прохождение проверки is_callable гарантирует не только наличие метода,
				 * но и его публичность.
				 */
			is_callable($callable)
			? call_user_func($callable, $value)
			: $value
		;
		df_result_string($result);
		return $result;
	}
}