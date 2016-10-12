<?php
class Df_WalletOne_Model_Request_SignatureGenerator extends Df_Core_Model {
	/** @return string */
	public function getSignature() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = base64_encode(pack('H*',md5(df_concat(
				$this->implodeParams(
					$this->convertParamsToWindows1251($this->sortParams($this->getSignatureParams()))
				)
				,$this->getEncryptionKey()
			))));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param array $params
	 * @return array
	 */
	private function convertParamsToWindows1251(array $params) {
		/** @var array $result */
		$result = array();
		foreach ($params as $key => $value) {
			/** @var string|int $key */
			/** @var mixed $value */

			$result[$key] =
				is_array($value)
				?
					$this->convertParamsToWindows1251($value)
				:
					(
							(
									is_string($value)
								&&
									/**
									 * Обратите внимание, что данный класс
									 * используется в двух сценариях:
									 *
									 * при отсылке запроса на проведение платежа платёжной системе
									 * и при получении от платёжной системы
									 * подтверждения приёма оплаты от покупателя.
									 *
									 * Во втором сценарии платёжная система
									 * присылает текстовые данные не в UTF-8,
									 * а в Windows-1251, и тогда iconv не нужна и, более того,
									 * приводит к сбою:
									 * Detected an illegal character in input string
									 */
									mb_detect_encoding($value, 'UTF-8', true)
							)
						? df_text()->convertUtf8ToWindows1251($value)
						: $value
					)
			;
		}
		df_result_array($result);
		return $result;
	}

	/** @return string */
	private function getEncryptionKey() {
		/** @var string $result */
		$result = $this->cfg(self::P__ENCRYPTION_KEY);
		df_result_string($result);
		return $result;
	}

	/** @return array */
	private function getSignatureParams() {
		/** @var array $result */
		$result = $this->cfg(self::P__SIGNATURE_PARAMS);
		df_result_array($result);
		return $result;
	}

	/**
	 * @param array $params
	 * @return string
	 */
	private function implodeParams(array $params) {
		/** @var array $result */
		$resultAsArray = null;
		foreach ($params as $key => $value) {
			/** @var string $key */
			/** @var mixed $value */
			df_assert_string($key);
			if (is_array($value)) {
				$value = implode($value);
			}
			$resultAsArray[$key] = $value;
		}
		/** @var string $result */
		$result = implode($resultAsArray);
		return $result;
	}

	/**
	 * @param array $params
	 * @return array
	 */
	private function sortParams(array $params) {
		/** @var array $result */
		$result = array();
		foreach ($params as $key => $value) {
			/** @var string $key */
			/** @var mixed $value */
			df_assert_string($key);
			if (is_array($value)) {
				usort($value, 'strcasecmp');
			}

			$result[$key] = $value;
		}

		uksort($result, 'strcasecmp');
		df_result_array($result);
		return $result;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__ENCRYPTION_KEY, self::V_STRING_NE)
			->_prop(self::P__SIGNATURE_PARAMS, self::V_ARRAY)
		;
	}
	const _CLASS = __CLASS__;
	const P__ENCRYPTION_KEY = 'encryption_key';
	const P__SIGNATURE_PARAMS = 'signature_params';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_WalletOne_Model_Request_SignatureGenerator
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}

}