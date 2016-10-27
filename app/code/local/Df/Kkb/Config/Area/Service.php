<?php
namespace Df\Kkb\Config\Area;
use Df_Directory_Model_Currency as Currency;
class Service extends \Df\Payment\Config\Area\Service {
	/** @return string */
	public function getCertificateId() {return dfc($this, function() {
		/** @var string $result */
		$result = $this->getVar('certificate_id');
		df_result_string_not_empty($result);
		return $result;
	});}
	
	/**
	 * @param string $code
	 * @return Currency
	 */
	public function getCurrencyByCodeInServiceFormat($code) {return dfc($this, function($code) {
		df_param_string_not_empty($code, 0);
		return Currency::ld(dfa(['398' => 'KZT', '840' => 'USD'], $code));
	}, func_get_args());}

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
	public function getCurrencyCodeInServiceFormat() {return dfc($this, function() {
		/** @var string $result */
		$result = dfa(['KZT' => '398', 'USD' => '840'], $this->getCurrencyCode());
		df_result_string_not_empty($result);
		return $result;
	});}

	/** @return string */
	public function getKeyPrivate() {return dfc($this, function() {
		/** @var string $result */
		$result = $this->getVar($this->testable('key_private'));
		df_result_string_not_empty($result);
		return $result;
	});}
	
	/** @return string */
	public function getKeyPrivatePassword() {return dfc($this, function() {
		/** @var string $result */
		$result = $this->getVar($this->testable('key_private_password'));
		// Тестовое значение хранится в config.xml в открытом виде,
		// поэтому для него дешифрация не нужна и приведёт к повреждению данных.
		return $this->isTestMode() ? $result : df_decrypt($result);
	});}

	/** @return string */
	public function getKeyPublic() {return dfc($this, function() {
		/** @var string $result */
		$result = $this->getVar('key_public');
		df_result_string_not_empty($result);
		return $result;
	});}

	/** @return string */
	public function getShopName() {return dfc($this, function() {
		/** @var string $result */
		$result = $this->getVar('shop_name');
		df_result_string_not_empty($result);
		return $result;
	});}

	/**
	 * @param string $variableName
	 * @return string
	 */
	private function testable($variableName) {return
		$this->isTestMode() ? df_c($variableName, '__test') : $variableName
	;}
}