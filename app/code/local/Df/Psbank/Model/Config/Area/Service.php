<?php
class Df_Psbank_Model_Config_Area_Service extends Df_Payment_Model_Config_Area_Service {
	/**
	 * @override
	 * @return string
	 */
	public function getRequestPassword() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->isTestMode()
				? $this->getVar($this->preprocessVar(self::KEY__VAR__REQUEST_PASSWORD))
				: df_text()->xor_($this->getRequestPasswordPart(1), $this->getRequestPasswordPart(2))
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getShopId() {
		/** @var string $result */
		$result = $this->getVar($this->preprocessVar(self::KEY__VAR__SHOP_ID));
		df_result_string_not_empty($result);
		return $result;
	}

	/** @return string */
	public function getShopName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getVar('shop_name');
			if (!$this->{__METHOD__})  {
				$this->{__METHOD__} = mb_substr(Mage::app()->getStore()->getFrontendName(), 0, 30);
			}
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getTerminalId() {
		/** @var string $result */
		$result = $this->getVar('terminal');
		df_result_string_not_empty($result);
		return $result;
	}

	/**
	 * @param int $partIndex
	 * @return string
	 */
	private function getRequestPasswordPart($partIndex) {
		df_assert(!$this->isTestMode());
		/** @var string $resultEncoded */
		$resultEncoded = $this->getVar('request_password_part_' . $partIndex);
		df_assert_string_not_empty($resultEncoded);
		return $this->decrypt($resultEncoded);
	}

	/**
	 * @param string $variableName
	 * @return string
	 */
	private function preprocessVar($variableName) {
		return $this->isTestMode() ? df_concat($variableName, '__test') : $variableName;
	}
}