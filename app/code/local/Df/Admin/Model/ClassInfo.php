<?php
class Df_Admin_Model_ClassInfo extends Df_Core_Model {
	/** @return string */
	public function getConfigFilePath() {return $this->cfg(self::P__CONFIG_FILE_PATH);}
	/**
	 * Для коллекций
	 * @override
	 * @return string
	 */
	public function getId() {return $this->getNameMf() ? $this->getNameMf() : $this->getName();}
	/** @return string */
	public function getModuleName() {return $this->cfg(self::P__MODULE_NAME);}
	/** @return string */
	public function getName() {return $this->cfg(self::P__NAME);}

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
	public function getNameMf() {return $this->cfg(self::P__NAME_MF);}
	/** @return string */
	public function getType() {return $this->cfg(self::P__TYPE);}

	/** @return string */
	private function getConstructor() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_a(
					array(
						self::TYPE__BLOCK => 'getBlockClassName'
						, self::TYPE__HELPER => 'getHelperClassName'
						, self::TYPE__MODEL => 'getModelClassName'
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
			->_prop(self::P__CONFIG_FILE_PATH, self::V_STRING)
			->_prop(self::P__MODULE_NAME, self::V_STRING)
			->_prop(self::P__NAME, self::V_STRING)
			->_prop(self::P__NAME_MF, self::V_STRING)
			->_prop(self::P__TYPE, self::V_STRING)
		;
	}
	const _CLASS = __CLASS__;
	const P__CONFIG_FILE_PATH = 'config_file_path';
	const P__MODULE_NAME = 'module_name';
	const P__NAME = 'name_';
	const P__NAME_MF = 'name_mf';
	const P__TYPE = 'type';
	const TYPE__BLOCK = 'block';
	const TYPE__HELPER = 'helper';
	const TYPE__MODEL = 'model';
	/**
	 * @param array(string => mixed) $parameters
	 * @return Df_Admin_Model_ClassInfo
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}