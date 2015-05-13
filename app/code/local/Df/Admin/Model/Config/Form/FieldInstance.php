<?php
/**
 * Класс @see Mage_Adminhtml_Block_System_Config_Form_Field
 * и все его потомки используются системой как объекты-одиночки.
 * Это не даёт нам возможность стандартным способом
 * реализовывать кэширование данных для объектов этого класса.
 * Данный класс нацелен решить эту проблему.
 */
class Df_Admin_Model_Config_Form_FieldInstance extends Df_Core_Model_Abstract {
	/**
	 * @param string $name
	 * @param bool $required [optional]
	 * @param string|int|float|null $defaultValue[optional]
	 * @return string|int|float|null
	 */
	public function getConfigParam($name, $required = false, $defaultValue = null) {
		/** @var string|int|float|null $result */
		$result = $this->getConfigParamInternal($name);
		if (is_null($result)) {
			if ($required) {
				df_error(
					'Требуется непустое значение для параметра «%s».'
					, $this->getConfigPath($name)
				);
			}
			else {
				$result = $defaultValue;
			}
		}
		return $result;
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function isConfigNodeExist($name) {
		if (!isset($this->{__METHOD__}[$name])) {
			df_param_string_not_empty($name, 0);
			$this->{__METHOD__}[$name] = rm_xml_child_exists($this->getConfig(), $name);
		}
		return $this->{__METHOD__}[$name];
	}

	/** @return Varien_Data_Form_Element_Abstract */
	public function getElement() {return $this->cfg(self::$P__ELEMENT);}

	/** @return Df_Admin_Block_System_Config_Form_Field_Custom */
	protected function getField() {return $this->cfg(self::$P__FIELD);}

	/** @return Mage_Core_Model_Config_Element */
	private function getConfig() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getElement()->getData('field_config');
			df_assert($this->{__METHOD__} instanceof Mage_Core_Model_Config_Element);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getConfigField() {return $this->getPartFromHtmlFieldName(2);}

	/** @return string */
	private function getConfigGroup() {return $this->getPartFromHtmlFieldName(0);}

	/**
	 * @param string $paramName
	 * @return string|int|float|null
	 */
	private function getConfigParamInternal($paramName) {
		if (!isset($this->{__METHOD__}[$paramName])) {
			$this->{__METHOD__}[$paramName] = rm_n_set(
				rm_xml_child_simple($this->getConfig(), $paramName)
			);
		}
		return rm_n_get($this->{__METHOD__}[$paramName]);
	}

	/**
	 * @param string $name
	 * @return string
	 */
	private function getConfigPath($name) {return rm_config_key($this->getConfigPathBase(), $name);}

	/** @return string */
	private function getConfigPathBase() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				rm_config_key(
					$this->getConfigSection(), $this->getConfigGroup(), $this->getConfigField()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getConfigSection() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * Нашёл только один способ узнать название секции...
			 * Наш объект содержит поле config_data следующей структуры:
					[config_data] => array(
						[df_1c/general/enabled] => 1
						[df_1c/general/enable_logging] => 0
						[df_1c/general/non_standard_currency_codes] => a:0:{}
						(...)
					)
			 * Берём первый элемент этого массива и вычленяем из этого элемента название секции.
			 */
			$this->{__METHOD__} =
				rm_first(
					explode(
						'/', rm_first(array_keys($this->getField()->getData('config_data')))
					)
				)
			;
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * Вычленяет из строки «groups[general][fields][url][value]»
	 * то, что стоит внутри квадратных скобок по индексу $partOrdering
	 * (индексы с нуля)
	 * @param int $partOrdering
	 * @return string
	 */
	private function getPartFromHtmlFieldName($partOrdering) {
		if (!isset($this->{__METHOD__}[$partOrdering])) {
			$this->{__METHOD__}[$partOrdering] =
				rm_first(
					explode(']', df_a(explode('[', $this->getElement()->getData('name')), $partOrdering + 1))
				)
			;
		}
		return $this->{__METHOD__}[$partOrdering];
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__ELEMENT, 'Varien_Data_Form_Element_Abstract')
			->_prop(self::$P__FIELD, Df_Admin_Block_System_Config_Form_Field_Custom::_CLASS)
		;
	}
	const _CLASS = __CLASS__;
	/** @var string */
	private static $P__ELEMENT = 'element';
	/** @var string */
	private static $P__FIELD = 'field';

	/**
	 * @param Df_Admin_Block_System_Config_Form_Field_Custom $field
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @param string $class
	 * @return Df_Admin_Model_Config_Form_FieldInstance
	 */
	public static function create(
		Df_Admin_Block_System_Config_Form_Field_Custom $field
		, Varien_Data_Form_Element_Abstract $element
		, $class
	) {
		/** @var Df_Admin_Model_Config_Form_FieldInstance $result */
		$result = new $class(array(self::$P__FIELD => $field, self::$P__ELEMENT => $element));
		df_assert($result instanceof Df_Admin_Model_Config_Form_FieldInstance);
		return $result;
	}
}