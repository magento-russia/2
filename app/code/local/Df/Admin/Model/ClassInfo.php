<?php
class Df_Admin_Model_ClassInfo extends Df_Core_Model {
	/** @return string */
	public function getConfigFilePath() {return $this->cfg(self::$P__CONFIG_FILE_PATH);}
	/**
	 * Для коллекций
	 * @override
	 * @return string
	 */
	public function getId() {return $this->getNameMf() ? $this->getNameMf() : $this->getName();}
	/** @return string */
	public function getModuleName() {return $this->cfg(self::$P__MODULE_NAME);}
	/** @return string */
	public function getName() {return $this->cfg(self::$P__NAME);}

	/** @return string */
	public function getNameByMf() {
		if (!isset($this->{__METHOD__})) {
			df_assert_string_not_empty($this->getNameMf());
			$this->{__METHOD__} =
				call_user_func(
					array(Mage::getConfig(), $this->getConstructor())
					,$this->getNameMf()
				)
			;
			if (!$this->{__METHOD__}) {
				df_error(
					'Не могу определить класс объекта «%s» типа «%s».'
					, $this->getNameMf()
					, $this->getType()
				);
			}
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getNameMf() {return $this->cfg(self::$P__NAME_MF);}
	/** @return string */
	public function getType() {return $this->cfg(self::$P__TYPE);}

	/** @return string */
	private function getConstructor() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				dfa(
					array(
						self::$TYPE__BLOCK => 'getBlockClassName'
						, self::$TYPE__HELPER => 'getHelperClassName'
						, self::$TYPE__MODEL => 'getModelClassName'
					)
					,$this->getType()
				)
			;
			if (!$this->{__METHOD__}) {
				df_error('Неизвестный тип класса: «%s».', $this->getType());
			}
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__CONFIG_FILE_PATH, RM_V_STRING)
			->_prop(self::$P__MODULE_NAME, RM_V_STRING)
			->_prop(self::$P__NAME, RM_V_STRING)
			->_prop(self::$P__NAME_MF, RM_V_STRING)
			->_prop(self::$P__TYPE, RM_V_STRING)
		;
	}
	/**
	 * @used-by Df_Admin_Model_ClassRewrite::_construct()
	 * @used-by Df_Admin_Model_ClassInfo_Collection::itemClass()
	 */
	const _C = __CLASS__;
	/** @var string */
	private static $P__CONFIG_FILE_PATH = 'config_file_path';
	/** @var string */
	private static $P__MODULE_NAME = 'module_name';
	/** @var string */
	private static $P__NAME = 'name_';
	/** @var string */
	private static $P__NAME_MF = 'name_mf';
	/** @var string */
	private static $P__TYPE = 'type';
	/** @var string */
	private static $TYPE__BLOCK = 'block';
	/** @var string */
	private static $TYPE__HELPER = 'helper';
	/** @var string */
	private static $TYPE__MODEL = 'model';

	/**
	 * @used-by Df_Admin_Model_ClassRewrite_Finder::getRewrites()
	 * @return array(string => string)
	 */
	public static function classTypeMap() {
		/** @var array(string => string) $r */
		static $r; return $r ? $r : $r = array(
			'blocks' => self::$TYPE__BLOCK
			, 'helpers' => self::$TYPE__HELPER
			, 'models' => self::$TYPE__MODEL
		);
	}

	/**
	 * @used-by Df_Admin_Model_ClassRewrite_Finder::parseRewrites()
	 * @param string $type
	 * @param string $name
	 * @param string $moduleName
	 * @param string $configFilePath
	 * @return Df_Admin_Model_ClassInfo
	 */
	public static function i($type, $name, $moduleName, $configFilePath) {
		return new self(array(
			self::$P__CONFIG_FILE_PATH => $configFilePath
			,self::$P__MODULE_NAME => $moduleName
			,self::$P__NAME => $name
			,self::$P__TYPE => $type
		));
	}

	/**
	 * @used-by Df_Admin_Model_ClassRewrite_Finder::parseRewrites()
	 * @param string $type
	 * @param string $nameMf
	 * @return Df_Admin_Model_ClassInfo
	 */
	public static function i_mf($type, $nameMf) {
		return new self(array(self::$P__NAME_MF => $nameMf, self::$P__TYPE => $type));
	}
}