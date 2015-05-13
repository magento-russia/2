<?php
abstract class Df_Payment_Model_Response_Xml extends Df_Payment_Model_Response {
	/** @return Df_Varien_Simplexml_Element */
	protected function e() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_xml($this->getXml());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $path
	 * @return Df_Varien_Simplexml_Element
	 */
	protected function getElement($path) {
		df_param_string_not_empty($path, 0);
		if (!isset($this->{__METHOD__}[$path])) {
			/** @var Df_Varien_Simplexml_Element $result */
			$result = $this->e()->descend($path);
			if (!($result instanceof Df_Varien_Simplexml_Element)) {
				df_error(
					"В документе XML отсутствует требуемый путь: «%s»\r\n"
					. "********************\r\n"
					. "%s\r\n"
					. "********************\r\n"
					, $path
					, $this->getXml()
				);
			}
			$this->{__METHOD__}[$path] = $result;
		}
		return $this->{__METHOD__}[$path];
	}

	/** @return string */
	protected function getXml() {return $this->cfg(self::P__XML);}
	
	/** @return Df_Core_Model_SimpleXml_Parser_Entity */
	protected function p() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Core_Model_SimpleXml_Parser_Entity::simple($this->e());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__XML, self::V_STRING_NE);
	}
	const _CLASS = __CLASS__;
	const P__XML = 'xml';
}