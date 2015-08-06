<?php
class Df_Core_Model_RemoteControl_Coder extends Df_Core_Model_Abstract {
	/**
	 * @param string $encodedData
	 * @return array
	 */
	public function decode($encodedData) {
		/** @var string $dataAsJson */
		$dataAsJson =
			/**
			 * Здесь надо использовать именно @see trim (а не @see df_trim или нечто другое).
			 * @link http://stackoverflow.com/a/3814674
			 * @link http://php.pe-tr.com/ru/php-mb_trim-unicode/
			 * trim нужен для отсекания «мусора» (символов NUL),
			 * который почему-то образуется на конце строки
			 */
			trim($this->getCryptor()->decrypt($encodedData))
		;
		df_assert_string($dataAsJson);
		/** @var array $result */
		$result =
			/**
			 * Zend_Json::decode использует json_decode при наличии расширения PHP JSON
			 * и свой внутренний кодировщик при отсутствии расширения PHP JSON.
			 * @see Zend_Json::decode
			 * @link http://stackoverflow.com/questions/4402426/json-encode-json-decode-vs-zend-jsonencode-zend-jsondecode
			 * Обратите внимание,
			 * что расширение PHP JSON не входит в системные требования Magento.
			 * @link http://www.magentocommerce.com/system-requirements
			 * Поэтому использование Zend_Json::decode выглядит более правильным, чем json_decode.
			 */
			Zend_Json::decode($dataAsJson)
		;
		df_result_array($result);
		return $result;
	}

	/**
	 * @param string $className
	 * @return string
	 */
	public function decodeClassName($className) {
		return $this->getCryptor()->decrypt(base64_decode($className));
	}

	/**
	 * @param array $data
	 * @return string
	 */
	public function encode(array $data) {
		/** @var string $dataAsJson */
		$dataAsJson =
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
			Zend_Json::encode($data)
		;
		df_assert_string($dataAsJson);
		/** @var string $result */
		$result =
			/**
			 * Обратите внимание, что mcrypt возвращает в результате кодирования бинарные данные,
			 * которые не являются текстом
			 * @link https://bugs.php.net/bug.php?id=42295
			 * @link http://stackoverflow.com/a/2174721
			 *
			 * Мы передаём по протоколу HTTP именно бинарные данные,
			 * пометив их заголовком application/octet-stream.
			 */
			$this->getCryptor()->encrypt($dataAsJson)
		;
		df_result_string($result);
		return $result;
	}

	/**
	 * @param string $className
	 * @return string
	 */
	public function encodeClassName($className) {
		return base64_encode($this->getCryptor()->encrypt($className));
	}

	/** @return Varien_Crypt_Mcrypt */
	private function getCryptor() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = new Varien_Crypt_Mcrypt();
			$this->{__METHOD__}->init('Антон Колесник — сосун членов');
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
	/** @return Df_Core_Model_RemoteControl_Coder */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}