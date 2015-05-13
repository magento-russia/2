<?php
class Df_Client_Model_Message_Request_Installed extends Df_Client_Model_Message_Request {
	/**
	 * @override
	 * @return string
	 */
	public function getActionClass() {return Dfa_Server_Model_Action_Installed::_CLASS;}

	/** @return string */
	public function getDomain() {return $this->cfg(self::P__DOMAIN);}

	/** @return string */
	public function getUrlBase() {return $this->cfg(self::P__URL__BASE);}

	/** @return string */
	public function getVersionMagento() {
		return $this->cfg(self::P__VERSION__MAGENTO);
	}

	/** @return string */
	public function getVersionPhp() {return $this->cfg(self::P__VERSION__PHP);}

	/** @return string */
	public function getVersionRm() {return $this->cfg(self::P__VERSION__RM);}

	/** @return Df_Client_Model_Message_Request_Installed */
	private function initEmpty() {
		$this->addData(array(
			self::P__DOMAIN => Mage::app()->getRequest()->getHttpHost()
			,self::P__URL__BASE => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB)
			,self::P__VERSION__MAGENTO => Mage::getVersion()
			,self::P__VERSION__PHP => phpversion()
			,self::P__VERSION__RM => rm_version()
		));
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__DOMAIN, self::V_STRING_NE)
			->_prop(self::P__URL__BASE, self::V_STRING_NE)
			->_prop(self::P__VERSION__MAGENTO, self::V_STRING_NE)
			->_prop(self::P__VERSION__PHP, self::V_STRING_NE)
			->_prop(self::P__VERSION__RM, self::V_STRING_NE)
		;
		if (!$this->getData()) {
			$this->initEmpty();
		}
	}
	const _CLASS = __CLASS__;
	const P__DOMAIN = 'domain';
	const P__URL__BASE = 'url__base';
	const P__VERSION__MAGENTO = 'version__magento';
	const P__VERSION__PHP = 'version__php';
	const P__VERSION__RM = 'version__rm';

	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Client_Model_Message_Request_Installed
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}