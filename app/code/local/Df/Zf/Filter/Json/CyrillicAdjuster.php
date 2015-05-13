<?php
class Df_Zf_Filter_Json_CyrillicAdjuster implements Zend_Filter_Interface
{
	/**
	 * @param string $value
	 * @return string
	 */
	public function filter($value) {
		df_param_string($value, 0);
		/** @var string $result */
		$result =
			df_text()->adjustCyrillicInJson(
				$value
			)
		;
		df_result_string($result);
		return $result;
	}
}