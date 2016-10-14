<?php
abstract class Df_Payment_Model_Response_Xml extends Df_Payment_Model_Response {
	/** @return Df_Core_Sxe */
	protected function e() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_xml($this->getXml());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $path
	 * @return Df_Core_Sxe
	 */
	protected function getElement($path) {
		df_param_string_not_empty($path, 0);
		if (!isset($this->{__METHOD__}[$path])) {
			/** @var Df_Core_Sxe $result */
			$result = $this->e()->descend($path);
			if (!($result instanceof Df_Core_Sxe)) {
				df_error(
					"В документе XML отсутствует требуемый путь: «%s»\n"
					. "********************\n"
					. "%s\n"
					. "********************\n"
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
	
	/** @return Df_Core_Xml_Parser_Entity */
	protected function p() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Core_Xml_Parser_Entity::entity($this->e());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__XML, RM_V_STRING_NE);
	}
	const _C = __CLASS__;
	const P__XML = 'xml';
}