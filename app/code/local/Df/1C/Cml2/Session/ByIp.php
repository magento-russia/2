<?php
class Df_1C_Cml2_Session_ByIp extends Df_Core_Model_Session_Custom_Additional {
	/**
	 * @param bool|null $value [optional]
	 * @return bool|null
	 */
	public function flag_catalogHasJustBeenExported($value = null) {
		/** @var bool|null $result */
		if (is_null($value)) {
			$result = !!$this->getData(self::$P__CATALOG_HAS_JUST_BEEN_EXPORTED);
		}
		else {
			$this->setData(self::$P__CATALOG_HAS_JUST_BEEN_EXPORTED, $value);
			$result = null;
		}
		return $result;
	}

	/**
	 * @param string $type
	 * @param string $id
	 * @return string
	 */
	public function getFilePathById($type, $id) {
		df_param_string_not_empty($type, 0);
		df_param_string_not_empty($id, 1);
		/** @var string $result */
		$result = dfa($this->getFileMap($type), $id);
		if (!$result) {
			df_error(
				'По какой-то причине файл типа «%s» с идентификатором «%s»'
				. ' не был зарегистрирован в системе.'
				. "\nРабота модуля невозможна."
				. "\nОбратитесь к разработчику."
				, $type, $id
			);
		}
		return $result;
	}

	/**
	 * @param string $type
	 * @param string $id
	 * @param string $path
	 * @return void
	 */
	public function setFilePathById($type, $id, $path) {
		df_param_string_not_empty($type, 0);
		df_param_string_not_empty($id, 1);
		df_param_string_not_empty($path, 2);
		$this->setData(self::$P__FILE_MAPS, array_merge($this->getFileMap(), array(
			$type => array_merge($this->getFileMap($type), array($id => $path)))
		));
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getSessionIdCustom() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$ipAddress = Mage::app()->getRequest()->getClientIp();
			df_assert_string_not_empty($ipAddress);
			/**
			 * Вызывая функцию @uses md5(), мы избавляемся от недопустимых символов
			 * в идентификаторе сессии.
			 * @see session_id()
			 * http://php.net/manual/function.session-id.php
			 */
			$this->{__METHOD__} = md5($ipAddress);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $type [optional]
	 * @return array(string => string)|array(array(string => string))
	 */
	private function getFileMap($type = null) {
		/** @var array(string => string) $maps */
		$maps = $this->getData(self::$P__FILE_MAPS);
		$maps = $maps ? $maps : array();
		return !$type ? $maps : dfa($maps, $type, array());
	}

	const NAME = __CLASS__;
	/** @var string */
	private static $P__CATALOG_HAS_JUST_BEEN_EXPORTED = 'catalog_has_just_been_exported';
	/** @var string */
	private static $P__FILE_MAPS = 'file_maps';

	/** @return Df_1C_Cml2_Session_ByIp */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}