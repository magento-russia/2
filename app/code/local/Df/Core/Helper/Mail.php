<?php
class Df_Core_Helper_Mail extends Mage_Core_Helper_Abstract {
	/** @return string */
	public function getCurrentStoreMailAddress() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = df_nts(Mage::getStoreConfig('trans_email/ident_general/email'));
			/** @var Zend_Validate_EmailAddress $mailValidator */
			$mailValidator = new Zend_Validate_EmailAddress();
			if (!$mailValidator->isValid($result)) {
				$result = 'noname@' . $this->getCurrentStoreDomain();
			}
			df_result_string($result);
			df_assert($mailValidator->isValid($result));
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getCurrentStoreDomain() {
		if (!isset($this->{__METHOD__})) {
			/** @var Zend_View_Helper_ServerUrl $helper */
			$helper = new Zend_View_Helper_ServerUrl();
			/** @var string|null $result */
			$result = $helper->getHost();
			if (!$result) {
				// Magento запущена с командной строки (например, планировщиком задач)
				/** @var string|null $baseUrl */
				$baseUrl = Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL);
				/**
				 * Тут уже нам некуда деваться:
				 * пусть уж администратор указывает базовый адрес в настройках.
				 */
				/** @var string $errorMessage */
				$errorMessage = 'Укажите полный корневой адрес магазина в административных настройках';
				df_assert($baseUrl, $errorMessage);
				try {
					/** @var Zend_Uri_Http $uri */
					$uri = Zend_Uri::factory($baseUrl);
					$result = $uri->getHost();
					df_assert_string_not_empty($result);
				}
				catch (Exception $e) {
					df_error($errorMessage);
				}
			}
			df_result_string_not_empty($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Core_Helper_Mail */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}