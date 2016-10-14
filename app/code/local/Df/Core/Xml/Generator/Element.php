<?php
/**
 * 2014-03-21
 * Пометил этот класс как абстрактный, потому что, как показал анализ кода,
 * экземпляры данного класса нигде не создаются,
 * и единственное применение этого класса на данный момент —
 * служить родителем классу @see Df_Core_Xml_Generator_Document.
 */
abstract class Df_Core_Xml_Generator_Element extends Df_Core_Model {
	/** @return Df_Core_Sxe */
	public function getElement() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->createElement();
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Core_Sxe */
	protected function createElement() {return rm_xml_node($this->getTagName(), $this->getAttributes());}

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
			->_prop(self::P__ATTRIBUTES, RM_V_ARRAY, false)
			->_prop(self::P__TAG_NAME, RM_V_STRING_NE)
		;
	}
	const _C = __CLASS__;
	const P__ATTRIBUTES = 'attributes';
	const P__TAG_NAME = 'tag_name';
}