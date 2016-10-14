<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
abstract class Df_Admin_Config_Source extends Df_Core_Model {
	/**
	 * @abstract
	 * @param bool $isMultiSelect [optional]
	 * @return string[]
	 */
	abstract protected function toOptionArrayInternal($isMultiSelect = false);

	/**
	 * @override
	 * @return string
	 */
	public function __a() {$a = func_get_args(); return rm_translate($a, 'Mage_Adminhtml');}

	/** @return Df_Admin_Config_Source */
	protected function reset() {
		unset($this->{__CLASS__ . '::getFieldCode'});
		unset($this->{__CLASS__ . '::getFieldConfigNode'});
		unset($this->{__CLASS__ . '::getFieldConfigNodeAsCanonicalArray'});
		unset($this->{__CLASS__ . '::getGroupCode'});
		unset($this->{__CLASS__ . '::getGroupConfigNode'});
		unset($this->{__CLASS__ . '::getPathExploded'});
		unset($this->{__CLASS__ . '::getSectionCode'});
		unset($this->{__CLASS__ . '::getSectionConfigNode'});
		return $this;
	}

	/**
	 * @used-by Mage_Adminhtml_Block_System_Config_Form::initFields()
	 * @param bool $isMultiSelect [optional]
	 * @return string[]
	 */
	public function toOptionArray($isMultiSelect = false) {
		$this->reset();
		return $this->toOptionArrayInternal($isMultiSelect);
	}

	/**
	 * @param bool $isMultiSelect [optional]
	 * @return array(array(string => string))
	 */
	public function toOptionArrayWithEmpty($isMultiSelect = false) {
		$this->reset();
		return array_merge(array(rm_option('', '')), $this->toOptionArrayInternal($isMultiSelect));
	}

	/** @return array(string|int => string) */
	public function toOptionArrayAssoc() {return rm_options_to_map($this->toOptionArray());}

	/**
	 * @param string $paramName
	 * @param mixed $defaultValue
	 * @return string|mixed
	 */
	protected function getFieldParam($paramName, $defaultValue = '') {
		return df_nts(dfa($this->getFieldConfigNodeAsCanonicalArray(), $paramName, $defaultValue));
	}

	/** @return string */
	protected function getPath() {return $this->cfg(self::$P__PATH);}

	/** @return string[] */
	protected function getPathExploded() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_explode_xpath($this->getPath());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getFieldCode() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = dfa($this->getPathExploded(), 2);
		}
		return $this->{__METHOD__};
	}

	/** @return Varien_Simplexml_Element */
	private function getFieldConfigNode() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_first($this->getGroupConfigNode()->xpath(df_cc_path(
				'fields', $this->getFieldCode())
			));
			df_assert($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return mixed[] */
	private function getFieldConfigNodeAsCanonicalArray() {
		if (!isset($this->{__METHOD__})) {
			/** @var mixed[] $result */
			$this->{__METHOD__} =
				$this->isCreatedStandalone()
				? $this->getData()
				: $this->getFieldConfigNode()->asCanonicalArray()
			;
			/**
			 * @uses Varien_Simplexml_Element::asCanonicalArray() может возвращать строку в случае,
			 * когда структура исходных данных не соответствует массиву.
			 */
			df_result_array($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getGroupCode() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = dfa($this->getPathExploded(), 1);
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return Varien_Simplexml_Element */
	private function getGroupConfigNode() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_first($this->getSectionConfigNode()->xpath(df_cc_path(
				'groups', $this->getGroupCode())
			));
			df_assert($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}
	/** @return string */
	private function getSectionCode() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = dfa($this->getPathExploded(), 0);
		}
		return $this->{__METHOD__};
	}

	/** @return Varien_Simplexml_Element */
	private function getSectionConfigNode() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_config_adminhtml()->getSection($this->getSectionCode());
			df_assert($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * true, если объект создан не системой, а вручную.
	 * В таком случае у объекта отсутствует информация о своём пути в настройках и т.п.
	 * @return bool
	 */
	private function isCreatedStandalone() {return !$this->cfg(self::$P__PATH);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__PATH, DF_V_STRING_NE, false);
	}
	/** @var string */
	private static $P__PATH = 'path';
}