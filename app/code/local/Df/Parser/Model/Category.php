<?php
class Df_Parser_Model_Category extends Df_Core_Model {
	/** @return string */
	public function getName() {
		return $this->cfg(self::P__NAME);
	}

	/** @return Zend_Uri_Http */
	public function getUri() {
		return $this->cfg(self::P__URI);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__NAME, self::V_STRING_NE)
			->_prop(self::P__URI, 'Zend_Uri_Http')
		;
	}
	const _CLASS = __CLASS__;
	const P__NAME = 'name';
	const P__URI = 'uri';
	/**
	 * @param Df_Parser_Model_Pq_Link $pq
	 * @return Df_Parser_Model_Category
	 */
	public static function createFromPqLink(Df_Parser_Model_Pq_Link $pq) {
		return self::i($pq->getName(), $pq->getUri());
	}
	/**
	 * @static
	 * @param string $name
	 * @param Zend_Uri_Http $uri
	 * @return Df_Parser_Model_Category
	 */
	public static function i($name, Zend_Uri_Http $uri) {
		return new self(array(self::P__NAME => $name, self::P__URI => $uri));
	}
}