<?php
abstract class Df_Kkb_Model_Response extends Df_Payment_Model_Response_Xml {
	/** @return Df_Kkb_Model_Config_Area_Service */
	protected function configS() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * Обратите внимание, что таким туповатым способом
			 * мы вправе создавать объект класса @see Df_Kkb_Model_Config_Area_Service
			 * лишь потому, что от него мы получаем только те данные,
			 * которые не зависят от области действия настроек.
			 */
			$this->{__METHOD__} = Df_Kkb_Model_Payment::i()->configS();
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	protected function getSignature() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = strrev(base64_decode($this->p()->descendS('bank_sign')));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return bool
	 */
	protected function isSuccessful() {
		if (!isset($this->{__METHOD__})) {
			$this->checkSignatureCorrect();
			$this->{__METHOD__} = !$this->getErrorMessage();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @throws Exception
	 * @return void
	 */
	private function checkSignatureCorrect() {
		if (!isset($this->{__METHOD__})) {
			/** @var resource $publicKeyResource */
			$publicKeyResource = openssl_get_publickey($this->configS()->getKeyPublic());
			df_assert(is_resource($publicKeyResource));
			try {
				/** @var int $verificationResult */
				$verificationResult =
					openssl_verify($this->getLetter(), $this->getSignature(), $publicKeyResource)
				;
				if (-1 === $verificationResult) {
					$this->throwException(
						'При проверке подписи прозошёл сбой: «%s».', openssl_error_string()
					);
				}
				if (0 === $verificationResult) {
					$this->throwException('Подпись неверна.');
				}
				if (1 !== $verificationResult) {
					$this->throwException(
						'Недопустимый результат проверки подписи: «%в».', $verificationResult
					);
				}
				$this->{__METHOD__} = true;
			}
			catch (Exception $e) {
				openssl_free_key($publicKeyResource);
				df_error($e);
			}
			openssl_free_key($publicKeyResource);
		}
	}
	
	/** @return string */
	private function getLetter() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				mb_substr(
					/**
					 * Функция @see mb_stristr() доступна, начиная с PHP 5.2
					 * (при этом должно быть включено расширение mbstring интерпретатора PHP)ю
					 * http://www.php.net/manual/en/function.mb-stristr.php
					 * Magento Community Edition требует PHP версии не ниже 5.2.13
					 * http://magento.com/resources/system-requirements
					 */
					mb_stristr($this->getXml(), '<bank')
					, 0
					, -mb_strlen(mb_stristr($this->getXml(), '<bank_sign'))
				)
			;
		}
		return $this->{__METHOD__};
	}


}