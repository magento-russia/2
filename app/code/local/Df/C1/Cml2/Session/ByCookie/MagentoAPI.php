<?php
namespace Df\C1\Cml2\Session\ByCookie;
class MagentoAPI extends \Mage_Api_Model_Session {
	/**
	 * В отличие от родительского метода @see Mage_Api_Model_Session::isSessionExpired()
	 * наш метод не обрывает сессию по таймауту.
	 * Раньше модуль 1С использовал стандартный класс @see Mage_Api_Model_Session,
	 * который по умолчанию обрывает сессию через час,
	 * что приводило к обрывам обмена данными между 1С и интернет-магазином:
	 * https://mail.google.com/mail/u/0/#search/%D1%81%D0%B8%D1%81%D1%82%D0%B5%D0%BC%D0%B0+%D0%BD%D0%B5+%D1%81%D0%BC%D0%BE%D0%B3%D0%BB%D0%B0+%D1%80%D0%B0%D1%81%D0%BF%D0%BE%D0%B7%D0%BD%D0%B0%D1%82%D1%8C+%D0%B0%D0%B4%D0%BC%D0%B8%D0%BD%D0%B8%D1%81%D1%82%D1%80%D0%B0%D1%82%D0%BE%D1%80%D0%B0
	 * Конечно, для решения проблемы можно увеличить значение таймаута
	 * «api/config/session_timeout»,
	 * однако я решил просто отключить этот таймаут именно для модуля 1С,
	 * нежели чем менять значение опции «api/config/session_timeout»
	 * сразу для всех возможных внешних систем, подключающихся к Magento.
	 * @override
	 * @param \Mage_Api_Model_User $user
	 * @return bool
	 */
	public function isSessionExpired($user) {return !$user->getId();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		/**
		 * Важно, чтобы Mage::getSingleton('api/session') возвращал именно этот объект,
		 * потому что классы ядра используют вызов Mage::getSingleton('api/session'):
		 * @see Mage_Api_Model_Resource_Abstract::_getSession().
		 */
		\Mage::register('_singleton/api/session', $this);
	}

	/** @return \Df\C1\Cml2\Session\ByCookie\MagentoAPI */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}