<?php
class Df_Kkb_Signer extends Df_Core_Model {
	/** @return string */
	public function getSignature() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = null;
			/** @var resource |false $pKey */
			$pKey = openssl_pkey_get_private(
				$this->configS()->getKeyPrivate()
				,$this->configS()->getKeyPrivatePassword()
			);
			if (!$pKey) {
				df_error(
					'Пароль не подходит к зашифрованному закрытому ключу.'
					.'<br/>Может быть, Вы неправильно указали их в административной части?'
					.'<br/>Если решить проблему не получается — сообщите об этом'
					. ' в <a href="http://magento-forum.ru/forum/324/">разделе модуля Казкоммерцбанка</a>'
					. ' форума Российской сборки Magento.'
				);
			}
			$r = openssl_sign($this->getDocument(), $result, $pKey);
			df_assert($r);
			$result = base64_encode(strrev($result));
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getDocument() {return $this->cfg(self::P__DOCUMENT);}

	/** @return Df_Kkb_Config_Area_Service */
	private function configS() {return $this->cfg(self::P__SERVICE_CONFIG);}
	
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		/** @var bool $openSslIsInstalled */
		static $openSslIsInstalled;
		if (is_null($openSslIsInstalled)) {
			/** http://searchcode.com/codesearch/view/15451657 */
			$openSslIsInstalled = extension_loaded('openssl') && function_exists('openssl_sign');
			if (!$openSslIsInstalled) {
				df_error(
					'Для работы модуля «Казкоммерцбанк» Вам нужно добавить к интерпретатору PHP'
					. ' криптографическое расширение OpenSSL (http://php.net/openssl).'
					. "\nИспользование OpenSSL является требованием Казкоммерцбанка,"
					. ' без этого работа модуля невозможна.'
				);
			}
		}
		parent::_construct();
		$this
		    ->_prop(self::P__DOCUMENT, DF_V_STRING_NE)
			->_prop(self::P__SERVICE_CONFIG, Df_Kkb_Config_Area_Service::class)
		;
	}

	const P__DOCUMENT = 'document';
	const P__SERVICE_CONFIG = 'service_config';
	/**
	 * @static
	 * @param string $document
	 * @param Df_Kkb_Config_Area_Service $serviceConfig
	 * @return Df_Kkb_Signer
	 */
	public static function i($document, Df_Kkb_Config_Area_Service $serviceConfig) {
		return new self(array(
			self::P__DOCUMENT => $document, self::P__SERVICE_CONFIG => $serviceConfig
		));
	}
}