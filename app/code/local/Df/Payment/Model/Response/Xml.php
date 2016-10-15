<?php
abstract class Df_Payment_Model_Response_Xml extends Df_Payment_Model_Response {
	/** @return \Df\Xml\X */
	protected function e() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_xml_parse($this->getXml());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $path
	 * @return \Df\Xml\X
	 */
	protected function getElement($path) {
		df_param_string_not_empty($path, 0);
		if (!isset($this->{__METHOD__}[$path])) {
			/** @var \Df\Xml\X $result */
			$result = $this->e()->descend($path);
			if (!($result instanceof \Df\Xml\X)) {
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
	
	/** @return \Df\Xml\Parser\Entity */
	protected function p() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = \Df\Xml\Parser\Entity::entity($this->e());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__XML, DF_V_STRING_NE);
	}

	const P__XML = 'xml';
}