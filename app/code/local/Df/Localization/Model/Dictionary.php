<?php
abstract class Df_Localization_Model_Dictionary extends Df_Core_Model_SimpleXml_Parser_Entity {
	/** @return string */
	abstract protected function getType();

	/**
	 * @override
	 * @return Df_Varien_Simplexml_Element
	 */
	public function getSimpleXmlElement() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_first(
				rm_xml_load_file($this->getPathFull())->xpath('/dictionary')
			);
			df_assert($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	protected function getPathFull() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_concat_path(
					Mage::getConfig()->getModuleDir('etc', 'Df_Localization')
					,'rm', 'dictionaries', $this->getType()
					, $this->getPathLocal()
				)
			;
			if (!file_exists($this->{__METHOD__})) {
				df_error('Не найден требуемый файл «%s».', $this->{__METHOD__});
			}
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getPathLocal() {return $this->cfg(self::$P__PATH_LOCAL);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__PATH_LOCAL, self::V_STRING_NE);
	}
	const _CLASS = __CLASS__;
	/** @var string */
	protected static $P__PATH_LOCAL = 'path_local';

	/**
	 * @param string $className
	 * @param string $pathLocal
	 * @return Df_Localization_Model_Onetime_Dictionary
	 */
	protected static function _i($className, $pathLocal) {
		return new $className(array(self::$P__PATH_LOCAL => $pathLocal));
	}
}