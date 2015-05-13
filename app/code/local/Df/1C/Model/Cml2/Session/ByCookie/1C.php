<?php
class Df_1C_Model_Cml2_Session_ByCookie_1C extends Df_Core_Model_Session_Custom_Primary {
	/** @return string|null */
	public function getFileName_Log() {return $this->getData(self::$P__FILE_NAME_LOG);}

	/**
	 * @param string $value
	 * @return Df_1C_Model_Cml2_Session_ByCookie_1C
	 */
	public function setFileName_Log($value) {
		$this->setData(self::$P__FILE_NAME_LOG, $value);
		return $this;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getSessionIdCustom() {return Df_1C_Model_Cml2_Cookie::s()->getSessionId();}

	/** @var string */
	private static $P__FILE_NAME_LOG = 'file_name_log';

	/** @return Df_1C_Model_Cml2_Session_ByCookie_1C */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}