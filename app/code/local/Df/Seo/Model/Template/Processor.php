<?php
class Df_Seo_Model_Template_Processor extends Df_Core_Model {
	/**
	 * Возвращает доступный в выражениях объект по имени.
	 * Выражении «product.manufacturer» имя объекта — «product», 
	 * и мы можем получить сам объект путём вызова
	 * $processor->getObject('product');
	 * @param string $name
	 * @return Varien_Object
	 */
	public function getObject($name) {return dfa($this->getObjects(), $name);}

	/** @return string */
	public function process() {return strtr($this->getText(), $this->getMappings());}

	/** @return array(string => Varien_Object) */
	protected function getObjects() {return $this->cfg(self::P__OBJECTS);}

	/** @return string */
	protected function getText() {return $this->cfg(self::P__TEXT);}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @used-by getExpressions()
	 * http://php.net/manual/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * http://3v4l.org/OipEQ
	 * @param array(string => mixed) $params
	 * @return Df_Seo_Model_Template_Expression
	 */
	private function createExpression(array $params) {
		return Df_Seo_Model_Template_Expression::i(array(
			Df_Seo_Model_Template_Expression::P__PROCESSOR => $this
			,Df_Seo_Model_Template_Expression::P__RAW => dfa($params, 0)
			,Df_Seo_Model_Template_Expression::P__CLEAN => dfa($params, 1)
		));
	}

	/** @return array */
	private function getExpressions() {
		$result =
			array_map(
				/** @uses createExpression() */
				array($this, 'createExpression')
				,preg_match_all(
					$this->getPattern()
					,$this->getText()
					,$matches
					,PREG_SET_ORDER
				)
				? $matches
				: array()
			)
		;
		return $result;
	}

	/**
	 * @uses Df_Seo_Model_Template_Expression::getRaw()
	 * @uses Df_Seo_Model_Template_Expression::getResult()
	 * @return array(string => string)
	 */
	private function getMappings() {return df_column($this->getExpressions(), 'getResult', 'getRaw');}

	/** @return string */
	private function getPattern() {return '#{([^}]+)}#mui';}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__TEXT, DF_V_STRING)
			->_prop(self::P__OBJECTS, DF_V_ARRAY)
		;
	}
	/** @used-by Df_Seo_Model_Template_Expression::_construct() */
	const _C = __CLASS__;
	const P__OBJECTS = 'objects';
	const P__TEXT = 'text';
	/**
	 * @static
	 * @param string $text
	 * @param array(string => Varien_Object) $objects
	 * @return Df_Seo_Model_Template_Processor
	 */
	public static function i($text, array $objects) {return new self(array(
		self::P__TEXT => $text, self::P__OBJECTS => $objects
	));}
}