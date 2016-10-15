<?php
class Df_Seo_Model_Template_Expression extends Df_Core_Model {
	/**
	 * Например, «product.manufacturer»
	 * @return string
	 */
	public function getClean() {return $this->cfg(self::P__CLEAN);}

	/** @return Varien_Object */
	public function getObject() {return $this->getProcessor()->getObject($this->getObjectName());}

	/**
	 * Например, «product» для выражения «product.manufacturer»
	 * @return string
	 */
	public function getObjectName() {return mb_strtolower(dfa($this->getCleanParts(), 0));}

	/** @return Df_Seo_Model_Template_Processor */
	public function getProcessor() {return $this->cfg(self::P__PROCESSOR);}

	/**
	 * Например, «manufacturer» для выражения «product.manufacturer»
	 * @return string
	 */
	public function getPropertyName() {return dfa($this->getCleanParts(), 1);}

	/**
	 * Результат вычисления выражения
	 * @return string
	 */
	public function getResult() {
		return
			$this->getAdapter()
			? $this->getAdapter()->getPropertyValue($this->getPropertyName())
			: $this->getRaw()
		;
	}

	/**
	 * Например,  «{product.manufacturer}»
	 * @return string
	 */
	public function getRaw() {return $this->cfg(self::P__RAW);}

	/** @return Df_Seo_Model_Template_Adapter */
	private function getAdapter() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_model(
				df_leaf_sne(df_config_node('df/seo/template/objects', $this->getObjectName(), 'adapter'))
				,array(Df_Seo_Model_Template_Adapter::P__EXPRESSION => $this)
			);
		}
		return $this->{__METHOD__};
	}

	/** @return string[] */
	private function getCleanParts() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_trim(explode('.', $this->getClean()));
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
			->_prop(self::P__PROCESSOR, Df_Seo_Model_Template_Processor::class)
			->_prop(self::P__RAW, DF_V_STRING)
			->_prop(self::P__CLEAN, DF_V_STRING)
		;
	}
	/** @used-by Df_Seo_Model_Template_Adapter::_construct() */

	const P__CLEAN = 'clean';
	const P__PROCESSOR = 'processor';
	const P__RAW = 'raw';
	/**
	 * @param array $parameters
	 * @return Df_Seo_Model_Template_Expression
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}