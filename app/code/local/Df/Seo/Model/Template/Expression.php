<?php
class Df_Seo_Model_Template_Expression extends Df_Core_Model_Abstract {
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
	public function getObjectName() {return mb_strtolower(df_a($this->getCleanParts(), 0));}

	/** @return Df_Seo_Model_Template_Processor */
	public function getProcessor() {return $this->cfg(self::P__PROCESSOR);}

	/**
	 * Например, «manufacturer» для выражения «product.manufacturer»
	 * @return string
	 */
	public function getPropertyName() {return df_a($this->getCleanParts(), 1);}

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
			$this->{__METHOD__} =
				df_model(
					$this->getAdapterClass()
					,array(Df_Seo_Model_Template_Adapter::P__EXPRESSION => $this)
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getAdapterClass() {
		/** @var string $valueAsString */
		$valueAsString = df()->config()->getNodeValueAsString($this->getConfigNode());
		return $valueAsString ? $valueAsString : null;
	}

	/**
	 * @return Mage_Core_Model_Config_Element|null
	*/
	private function getConfigNode() {
		return df()->config()->getNodeByKey(
			'df/seo/template/objects/' . $this->getObjectName() . '/adapter'
		);
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
			->_prop(self::P__PROCESSOR, Df_Seo_Model_Template_Processor::_CLASS)
			->_prop(self::P__RAW, self::V_STRING)
			->_prop(self::P__CLEAN, self::V_STRING)
		;
	}
	const _CLASS = __CLASS__;
	const P__CLEAN = 'clean';
	const P__PROCESSOR = 'processor';
	const P__RAW = 'raw';
	/**
	 * @param array $parameters
	 * @return Df_Seo_Model_Template_Expression
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}