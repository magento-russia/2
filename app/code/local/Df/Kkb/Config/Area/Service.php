<?php
class Df_Kkb_Config_Area_Service extends \Df\Payment\Config\Area\Service {
	/** @return string */
	public function getCertificateId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getVar('certificate_id');
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
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
					dfa(array('398' => 'KZT', '840' => 'USD'), $codeInServiceFormat)
				)
			;
		}
		return $this->{__METHOD__}[$codeInServiceFormat];
	}

	/**
	 * Платёжный шлюз Казкоммерцбанка, согласно своей документации,
	 * помимо казахского тенге позволяет выставлять счёт в долларах.
	 * Однако, оплата счёта в долларах почему-то приводит к сбою
	 * «Ошибка авторизации» с кодом «-19».
	 * @override
	 * @return string
	 */
	public function getCurrencyCode() {return 'KZT';}

	/**
	 * @override
	 * @return string
	 */
	public function getCurrencyCodeInServiceFormat() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = dfa(array('KZT' => '398', 'USD' => '840'), $this->getCurrencyCode());
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getKeyPrivate() {
		/** @var string $result */
		$result = $this->getVar($this->testable('key_private'));
		df_result_string_not_empty($result);
		return $result;
	}
	
	/** @return string */
	public function getKeyPrivatePassword() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$this->{__METHOD__} = $this->getVar($this->testable('key_private_password'));
			// Тестовое значение хранится в config.xml в открытом виде,
			// поэтому для него дешифрация не нужна и приведёт к повреждению данных.
			$this->{__METHOD__} = $this->isTestMode() ? $result : df_decrypt($result);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getKeyPublic() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getVar('key_public');
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getShopName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getVar('shop_name');
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $variableName
	 * @return string
	 */
	private function testable($variableName) {
		return $this->isTestMode() ? df_c($variableName, '__test') : $variableName;
	}

	/** @used-by Df_Kkb_Signer::_construct */

}