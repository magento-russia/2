<?php
namespace Df\Xml\Generator;
/**
 * 2014-03-21
 * Пометил этот класс как абстрактный, потому что, как показал анализ кода,
 * экземпляры данного класса нигде не создаются,
 * и единственное применение этого класса на данный момент —
 * служить родителем классу @see \Df\Xml\Generator\Document.
 */
abstract class Element extends \Df_Core_Model {
	/** @return \Df\Xml\X */
	public function getElement() {return dfc($this, function() {return $this->createElement();});}

	/** @return \Df\Xml\X */
	protected function createElement() {return
		df_xml_node($this->tag(), $this->getAttributes())
	;}

	/** @return array(string => string) */
	protected function getAttributes() {return $this[self::P__ATTRIBUTES];}

	/** @return string */
	protected function tag() {return $this[self::P__TAG_NAME];}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__ATTRIBUTES, DF_V_ARRAY, false)
			->_prop(self::P__TAG_NAME, DF_V_STRING_NE)
		;
	}
	
	const P__ATTRIBUTES = 'attributes';
	const P__TAG_NAME = 'tag_name';
}