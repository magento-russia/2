<?php
abstract class Df_Core_Model_Form extends Df_Core_Model_Abstract {
	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getZendFormClass();

	/**
	 * @param $key
	 * @param string $default[optional]
	 * @return mixed
	 */
	protected function getField($key, $default = null) {
		/** @var Zend_Form_Element|null $element */
		$element = $this->getZendForm()->getElement($key);
		if (!$element) {
			df_error('Не найдено поле: «%s».', $key);
		}
		$result = $element->getValue();
		$validator = new Zend_Validate_NotEmpty();
		if (!$validator->isValid($result)) {
			$result = $default;
		}
		return $result;
	}

	/**
	 * @param string $key
	 * @return string[]
	 */
	protected function getTextareaParam($key) {return df_text()->parseTextarea($this->getField($key));}

	/** @return Df_Zf_Form */
	private function getZendForm() {
		if (!isset($this->{__METHOD__})) {
			$class = $this->getZendFormClass();
			/** @var Df_Zf_Form $result */
			$result = new $class;
			df_assert($result instanceof Df_Zf_Form);
			$result->setValues($this->getZendFormValues());
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return array */
	private function getZendFormValues() {return $this->cfg(self::P__ZEND_FORM_VALUES);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__ZEND_FORM_VALUES, self::V_ARRAY);
	}
	const _CLASS = __CLASS__;
	const P__ZEND_FORM_VALUES = 'zend_form_values';
}