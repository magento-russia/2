<?php
/**
 * 2014-03-21
 * Пометил этот класс как абстрактный, потому что, как показал анализ кода,
 * экземпляры данного класса нигде не создаются,
 * и единственное применение этого класса на данный момент —
 * служить родителем классу @see Df_Core_Model_SimpleXml_Generator_Document.
 */
abstract class Df_Core_Model_SimpleXml_Generator_Element extends Df_Core_Model_Abstract {
	/** @return Df_Varien_Simplexml_Element */
	public function getElement() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->createElement();
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Varien_Simplexml_Element */
	protected function createElement() {
		return Df_Varien_Simplexml_Element::createNode($this->getTagName(), $this->getAttributes());
	}

	/** @return array(string => string) */
	protected function getAttributes() {return $this->cfg(self::P__ATTRIBUTES, array());}

	/** @return string */
	protected function getTagName() {return $this->cfg(self::P__TAG_NAME);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__ATTRIBUTES, self::V_ARRAY, false)
			->_prop(self::P__TAG_NAME, self::V_STRING_NE)
		;
	}
	const _CLASS = __CLASS__;
	const P__ATTRIBUTES = 'attributes';
	const P__TAG_NAME = 'tag_name';
}