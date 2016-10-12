<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
abstract class Df_Admin_Model_Config_Source extends Df_Core_Model {
	/**
	 * @abstract
	 * @param bool $isMultiSelect[optional]
	 * @return string[]
	 */
	abstract protected function toOptionArrayInternal($isMultiSelect = false);

	/** @return Df_Admin_Model_Config_Source */
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
	 * @param bool $isMultiSelect[optional]
	 * @return string[]
	 */
	public function toOptionArray($isMultiSelect = false) {
		$this->reset();
		return $this->toOptionArrayInternal($isMultiSelect);
	}

	/**
	 * @param bool $isMultiSelect[optional]
	 * @return string[]
	 */
	public function toOptionArrayWithEmpty($isMultiSelect = false) {
		$this->reset();
		/** @var string[] $result */
		$result = $this->toOptionArrayInternal($isMultiSelect);
		array_unshift($result, array('label' => '', 'value' => ''));
		return $result;
	}

	/** @return string[] */
	public function toOptionArrayAssoc() {
		/** @var string[] $result */
		$result = array();
		foreach ($this->toOptionArray() as $option) {
			/** @var string[] $option */
			$result[df_a($option, self::OPTION_KEY__VALUE)] = df_a($option, self::OPTION_KEY__LABEL);
		}
		return $result;
	}

	/**
	 * @param string $paramName
	 * @param mixed $defaultValue
	 * @return string|mixed
	 */
	protected function getFieldParam($paramName, $defaultValue = '') {
		return df_nts(df_a($this->getFieldConfigNodeAsCanonicalArray(), $paramName, $defaultValue));
	}

	/** @return string */
	protected function getPath() {return $this->cfg(self::P__PATH);}

	/** @return string[] */
	protected function getPathExploded() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df()->config()->explodeKey($this->getPath());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getFieldCode() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_a($this->getPathExploded(), 2);
		}
		return $this->{__METHOD__};
	}

	/** @return Varien_Simplexml_Element */
	private function getFieldConfigNode() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				rm_first(
					$this->getGroupConfigNode()->xpath(
						df_concat_xpath('fields', $this->getFieldCode())
					)
				)
			;
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
			 * @see Varien_Simplexml_Element::asCanonicalArray может возвращать строку в случае,
			 * когда структура исходных данных не соответствует массиву.
			 */
			df_result_array($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getGroupCode() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_a($this->getPathExploded(), 1);
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return Varien_Simplexml_Element */
	private function getGroupConfigNode() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				rm_first(
					$this->getSectionConfigNode()->xpath(
						df_concat_xpath('groups', $this->getGroupCode())
					)
				)
			;
			df_assert($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}
	/** @return string */
	private function getSectionCode() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_a($this->getPathExploded(), 0);
		}
		return $this->{__METHOD__};
	}

	/** @return Varien_Simplexml_Element */
	private function getSectionConfigNode() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_mage()->adminhtml()->getConfig()->getSection($this->getSectionCode());
			df_assert($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * true, если объект создан не системой, а вручную.
	 * В таком случае у объекта отсутствует информация о своём пути в настройках и т.п.
	 * @return bool
	 */
	private function isCreatedStandalone() {return !$this->cfg(self::P__PATH);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__PATH, self::V_STRING_NE, false);
	}
	const _CLASS = __CLASS__;
	const P__PATH = 'path';
	const OPTION_KEY__LABEL = 'label';
	const OPTION_KEY__VALUE = 'value';

}