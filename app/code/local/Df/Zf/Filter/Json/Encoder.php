<?php
class Df_Zf_Filter_Json_Encoder implements Zend_Filter_Interface
{
	/**
	 * @param mixed $value
	 * @return mixed
	 */
	public function filter($value) {
		/** @var string $result */
		$result =
			/**
			 * Zend_Json::encode использует json_encode при наличии расширения PHP JSON
			 * и свой внутренний кодировщик при отсутствии расширения PHP JSON.
			 * @see Zend_Json::encode
			 * @link http://stackoverflow.com/questions/4402426/json-encode-json-decode-vs-zend-jsonencode-zend-jsondecode
			 * Обратите внимание,
			 * что расширение PHP JSON не входит в системные требования Magento.
			 * @link http://www.magentocommerce.com/system-requirements
			 * Поэтому использование Zend_Json::encode выглядит более правильным, чем json_encode.
			 */
			Zend_Json::encode($value)
		;
		df_result_string($result);
		return $result;
	}
}