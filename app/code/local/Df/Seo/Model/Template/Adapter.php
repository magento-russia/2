<?php
abstract class Df_Seo_Model_Template_Adapter extends Df_Core_Model {
	/**
	 * @param string $propertyName
	 * @return Df_Seo_Model_Template_Property
	 */
	abstract protected function getPropertyClass($propertyName);

	/** @return string */
	public function getName() {
		return $this->getExpression()->getObjectName();
	}

	/** @return Varien_Object */
	public function getObject() {
		return $this->getExpression()->getObject();
	}

	/** @return Df_Seo_Model_Template_Processor */
	public function getProcessor() {
		return $this->getExpression()->getProcessor();
	}

	/**
	 * Результат вычисления выражения
	 *
	 * @param string $propertyName
	 * @return string
	 */
	public function getPropertyValue($propertyName) {
		return
				!$this->getProperty($propertyName)
			?
				null
			:
				$this->getProperty($propertyName)->getValue()
		;
	}
	/** @var array */
	private $_properties = array();

	/** @return Df_Seo_Model_Template_Expression */
	protected function getExpression() {
		return $this->cfg(self::P__EXPRESSION);
	}

	/**
	 * @param string $propertyName
	 * @return Df_Seo_Model_Template_Property
	 */
	protected function getProperty($propertyName) {
		if (!isset($this->{__METHOD__}[$propertyName])) {
			$this->{__METHOD__}[$propertyName] =
					!$this->getPropertyClass($propertyName)
				?
					null
				:
					df_model(
						$this->getPropertyClass($propertyName)
						,array(
							Df_Seo_Model_Template_Property::P__ADAPTER => $this
							,Df_Seo_Model_Template_Property::P__NAME => $propertyName
						)
					)
			;
		}
		return $this->{__METHOD__}[$propertyName];
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__EXPRESSION, Df_Seo_Model_Template_Expression::_CLASS);
	}
	const _CLASS = __CLASS__;
	const P__EXPRESSION = 'expression';
}