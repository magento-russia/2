<?php
class Df_Core_Model_Filter_Adapter extends Df_Core_Model_Abstract implements Zend_Filter_Interface {
	/**
	 * @override
	 * @param mixed $value
	 * @return mixed
	 */
	public function filter($value) {
		$this->getAdapteeInstance()->setData($this->getParamNameForFilteredValue(), $value);
		return call_user_func(array($this->getAdapteeInstance(), $this->getAdapteeMethod()));
	}

	/** @return Varien_Object */
	private function getAdapteeInstance() {return $this->cfg(self::P__ADAPTEE_INSTANCE);}

	/** @return string */
	private function getAdapteeMethod() {return $this->cfg(self::P__ADAPTEE_METHOD);}

	/** @return string */
	private function getParamNameForFilteredValue() {
		return $this->cfg(self::P__PARAM__NAME_FORM_FILTERED_VALUE);
	}

 	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__ADAPTEE_INSTANCE, Df_Varien_Const::OBJECT_CLASS)
			->_prop(self::P__ADAPTEE_METHOD, self::V_STRING_NE)
			->_prop(self::P__PARAM__NAME_FORM_FILTERED_VALUE, self::V_STRING)
		;
	}
	const _CLASS = __CLASS__;
	const P__ADAPTEE_INSTANCE = 'adaptee_instance';
	const P__ADAPTEE_METHOD = 'adaptee_method';
	const P__PARAM__NAME_FORM_FILTERED_VALUE = 'param_name_form_filtered_value';

	/**
	 * @static
	 * @param Varien_Object $adapteeInstance
	 * @param string $adapteeMethod
	 * @param string $paramNameForFilteredValue
	 * @return Df_Core_Model_Filter_Adapter
	 */
	public static function i(Varien_Object $adapteeInstance, $adapteeMethod, $paramNameForFilteredValue) {
		return new self(array(
			self::P__ADAPTEE_INSTANCE => $adapteeInstance
			,self::P__ADAPTEE_METHOD => $adapteeMethod
			,self::P__PARAM__NAME_FORM_FILTERED_VALUE => $paramNameForFilteredValue
		));
	}
}