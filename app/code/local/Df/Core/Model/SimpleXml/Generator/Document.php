<?php
class Df_Core_Model_SimpleXml_Generator_Document extends Df_Core_Model_SimpleXml_Generator_Element {
	/** @return string */
	public function getXml() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result =
				$this->needSkipXmlHeader()
				? $this->getElement()->asXMLPart()
				: $this->getElement()->asXML()
			;
			// Убеждаемся, что asXML вернуло строку, а не false.
			df_assert_string($result);
			/**
			 * символ 0xB (вертикальная табуляция) допустим в UTF-8,
			 * но недопустим в XML
			 * @link http://stackoverflow.com/a/10095901/254475
			 */
			$result = str_replace("\x0B", "&#x0B;", $result);
			if ($this->hasEncodingWindows1251()) {
				$result = df_text()->convertUtf8ToWindows1251($result);
			}
			if ($this->needRemoveLineBreaks()) {
				$result = df_text()->removeLineBreaks($result);
			}
			if ($this->needDecodeEntities()) {
				$result = html_entity_decode($result, ENT_NOQUOTES, 'UTF-8');
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function hasEncodingWindows1251() {return false;}

	/**
	 * @override
	 * @return Df_Varien_Simplexml_Element
	 */
	protected function createElement() {
		/** @var Df_Varien_Simplexml_Element $result */
		$result = rm_xml(rm_concat_clean(Df_Core_Const::T_NEW_LINE
			,$this->getXmlHeader(), $this->getDocType(), $this->getTag()
		));
		$result->addAttributes($this->getAttributes());
		$result->importArray($this->getContentsAsArray(), $this->needWrapInCDataAll());
		return $result;
	}

	/** @return array(string => mixed) */
	protected function getContentsAsArray() {
		return $this->cfg(self::P__CONTENTS_AS_ARRAY, array());
	}

	/** @return string */
	protected function getDocType() {return $this->cfg(self::P__DOC_TYPE, '');}

	/** @return bool */
	protected function needDecodeEntities() {
		return $this->cfg(self::P__NEED_DECODE_ENTITIES, false);
	}

	/** @return bool */
	protected function needRemoveLineBreaks() {
		return $this->cfg(self::P__NEED_REMOVE_LINE_BREAKS, false);
	}

	/** @return bool */
	protected function needSkipXmlHeader() {
		return $this->cfg(self::P__NEED_SKIP_XML_HEADER, false);
	}

	/** @return bool */
	protected function needWrapInCDataAll() {
		return $this->cfg(self::P__NEED_WRAP_IN_CDATA_ALL, false);
	}

	/** @return string */
	private function getTag() {return rm_sprintf('<%s/>', $this->getTagName());}

	/** @return string */
	private function getXmlHeader() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->needSkipXmlHeader()
				? ''
				: str_replace(
					'{encoding}'
					, $this->hasEncodingWindows1251() ? 'windows-1251' : 'utf-8'
					, "<?xml version='1.0' encoding='{encoding}'?>"
				)
			;
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
			->_prop(self::P__CONTENTS_AS_ARRAY, self::V_ARRAY, false)
			->_prop(self::P__DOC_TYPE, self::V_STRING, false)
			->_prop(self::P__NEED_DECODE_ENTITIES, self::V_BOOL, false)
			->_prop(self::P__NEED_REMOVE_LINE_BREAKS, self::V_BOOL, false)
			->_prop(self::P__NEED_SKIP_XML_HEADER, self::V_BOOL, false)
			->_prop(self::P__NEED_WRAP_IN_CDATA_ALL, self::V_BOOL, false)
		;
	}
	const _CLASS = __CLASS__;
	const P__CONTENTS_AS_ARRAY = 'contents_as_array';
	const P__DOC_TYPE = 'doc_type';
	const P__NEED_DECODE_ENTITIES = 'need_decode_entities';
	const P__NEED_REMOVE_LINE_BREAKS = 'need_remove_line_breaks';
	const P__NEED_SKIP_XML_HEADER = 'need_skip_xml_header';
	const P__NEED_WRAP_IN_CDATA_ALL = 'need_wrap_in_cdata_all';
	/**
	 * Добавил подчёркивание к названию этого метода,
	 * чтобы метод не конфликтовал с методом i() дочерних классов.
	 * @param array(string => mixed) $parameters
	 * @return Df_Core_Model_SimpleXml_Generator_Document
	 */
	public static function _i(array $parameters = array()) {return new self($parameters);}
}