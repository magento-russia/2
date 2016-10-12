<?php
class Df_Core_Model_Output_Html_A extends Df_Core_Model {
	/**
	 * @override
	 * @return string
	 */
	public function __toString() {
		try {
			$result = !$this->hasHref() ? $this->getAnchor() : $this->getRealTag();
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e, false);
			$result = df_escape('<invalid link>');
		}
		return $result;
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	private function getRealTag() {
		$result = '<a href=' . $this->q($this->getHref());
		if ($this->getTarget()) {
			$result .= (' target=' . $this->q($this->getTarget()));
		}
		if ($this->getClasses()) {
			$result .= (' class=' . $this->q($this->getClassesAsAttributeValue()));
		}
		if ($this->getTitle()) {
			$result .= (' title=' . $this->q($this->getTitle()));
		}
		if ($this->getRel()) {
			$result .= (' rel=' . $this->q($this->getRel()));
		}
		$result .= ('>' . $this->getAnchor() . '</a>');
		return $result;
	}

	/** @return string */
	public function getAnchor() {return $this->filterForOutput($this->cfg(self::P__ANCHOR));}

	/** @return array */
	public function getClasses() {return $this->cfg(self::P__CLASSES, array());}

	/** @return string */
	public function getHref() {return $this->filterForOutput($this->cfg(self::P__HREF));}

	/** @return string */
	public function getQuote() {return $this->cfg(self::P__QUOTE, "'");}

	/** @return string */
	public function getRel() {return $this->filterForOutput($this->cfg(self::P__REL));}

	/** @return string */
	public function getTarget() {return $this->cfg(self::P__TARGET);}

	/** @return string */
	public function getTitle() {return $this->filterForOutput($this->cfg(self::P__TITLE));}

	/** @return bool */
	public function hasHref() {return !!$this->getHref();}

	/**
	 * @param $text
	 * @return string
	 */
	private function filterForOutput($text) {return df_escape(df_trim($text));}

	/** @return string */
	private function getClassesAsAttributeValue() {
		return $this->filterForOutput(implode(',', df_trim($this->getClasses())));
	}

	/**
	 * @param string $text
	 * @return string
	 */
	private function q($text) {return df_text()->quote($text, $this->getQuote());}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__ANCHOR, self::V_STRING_NE)
			->_prop(self::P__CLASSES, self::V_STRING, false)
			->_prop(self::P__HREF, self::V_STRING, false)
			->_prop(self::P__QUOTE, self::V_STRING, false)
			->_prop(self::P__TARGET, self::V_STRING, false)
			->_prop(self::P__TITLE, self::V_STRING, false)
			->_prop(self::P__REL, self::V_STRING, false)
		;
	}
	const _CLASS = __CLASS__;
	const P__ANCHOR = 'anchor';
	const P__CLASSES = 'classes';
	const P__HREF = 'href';
	const P__QUOTE = 'quote';
	const P__TARGET = 'target';
	const P__TITLE = 'title';
	const P__REL = 'rel';
	const TARGET__BLANK = '_blank';

	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return string
	 */
	public static function output(array $parameters = array()) {
		/** @var Df_Core_Model_Output_Html_A $i */
		$i = new self($parameters);
		return $i->__toString();
	}
}