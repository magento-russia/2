<?php
namespace Df\C1\Cml2\Session\ByCookie;
class C1 extends \Df_Core_Model_Session_Custom_Primary {
	/**
	 * @used-by
	 * @return string|null
	 */
	public function getFileName_Log() {return $this->getData(self::$P__FILE_NAME_LOG);}

	/**
	 * @param string $value
	 * @return $this
	 */
	public function setFileName_Log($value) {
		$this->setData(self::$P__FILE_NAME_LOG, $value);
		return $this;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getSessionIdCustom() {return \Df\C1\Cml2\Cookie::s()->getSessionId();}

	/** @var string */
	private static $P__FILE_NAME_LOG = 'file_name_log';

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}