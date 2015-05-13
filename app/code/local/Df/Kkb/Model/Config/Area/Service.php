<?php
class Df_Kkb_Model_Config_Area_Service extends Df_Payment_Model_Config_Area_Service {
	/** @return string */
	public function getCertificateId() {
		/** @var string $result */
		$result = $this->getVar($this->preprocessVar('certificate_id'));
		df_result_string_not_empty($result);
		return $result;
	}
	
	/**
	 * @param string $codeInServiceFormat
	 * @return Df_Directory_Model_Currency
	 */
	public function getCurrencyByCodeInServiceFormat($codeInServiceFormat) {
		df_param_string_not_empty($codeInServiceFormat, 0);
		if (!isset($this->{__METHOD__}[$codeInServiceFormat])) {
			$this->{__METHOD__}[$codeInServiceFormat] =
				Df_Directory_Model_Currency::ld(
					df_a(array('398' => 'KZT', '840' => 'USD'), $codeInServiceFormat)
				)
			;
		}
		return $this->{__METHOD__}[$codeInServiceFormat];
	}

	/**
	 * @override
	 * @return string
	 */
	public function getCurrencyCode() {
		// Платёжный шлюз Казкоммерцбанка, согласно своей документации,
		// помимо казахского тенге позволяет выставлять счёт в долларах.
		// Однако, оплата счёта в долларах почему-то приводит к сбою
		// «Ошибка авторизации» с кодом «-19».
		return 'KZT';
	}

	/**
	 * @override
	 * @return string
	 */
	public function getCurrencyCodeInServiceFormat() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_a(array('KZT' => '398', 'USD' => '840'), $this->getCurrencyCode());
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getKeyPrivate() {
		/** @var string $result */
		$result = $this->getVar($this->preprocessVar('key_private'));
		df_result_string_not_empty($result);
		return $result;
	}
	
	/** @return string */
	public function getKeyPrivatePassword() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getVar($this->preprocessVar('key_private_password'));
			/**
			 * Тестовое значение хранится в config.xml в открытом виде,
			 * поэтому для него дешифрация не нужна и приведёт к повреждению данных.
			 */
			if (!$this->isTestMode()) {
				$this->{__METHOD__} = $this->decrypt($this->{__METHOD__});
			}
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getKeyPublic() {
		/** @var string $result */
		$result = $this->getVar('key_public');
		df_result_string_not_empty($result);
		return $result;
	}

	/**
	 * @override
	 * @return string
	 */
	public function getShopId() {
		/** @var string $result */
		$result = $this->getVar($this->preprocessVar(self::KEY__VAR__SHOP_ID));
		df_result_string_not_empty($result);
		return $result;
	}

	/** @return string */
	public function getShopName() {
		/** @var string $result */
		$result = $this->getVar($this->preprocessVar('shop_name'));
		df_result_string_not_empty($result);
		return $result;
	}

	/**
	 * @param string $variableName
	 * @return string
	 */
	private function preprocessVar($variableName) {
		return $this->isTestMode() ? df_concat($variableName, '__test') : $variableName;
	}

	const _CLASS = __CLASS__;
}