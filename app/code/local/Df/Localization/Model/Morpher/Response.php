<?php
class Df_Localization_Model_Morpher_Response extends Df_Core_Model_SimpleXml_Parser_Entity {
	/** @return string */
	public function getErrorMessage() {return $this->descendS('message');}

	/** @return string */
	public function getGenderAsText() {return $this->getEntityParam('род');}
	/**
	 * @override
	 * @return string
	 */
	public function getId() {return $this->getInCaseNominative();}
	/** @return string */
	public function getInCaseAccusative() {return $this->getEntityParam('В');}
	/** @return string */
	public function getInCaseDative() {return $this->getEntityParam('Д');}
	/** @return string */
	public function getInCaseGenitive() {return $this->getEntityParam('Р');}
	/** @return string */
	public function getInCaseInstrumental() {return $this->getEntityParam('Т');}
	/** @return string */
	public function getInCaseNominative() {return $this->cfg(self::P__CASE_NOMINATIVE);}
	/** @return string */
	public function getInCasePrepositional() {return $this->getEntityParam('П');}
	/** @return string */
	public function getInCasePrepositionalWithPreposition() {return $this->getEntityParam('П-о');}
	/** @return string */
	public function getInFormOrigin() {return $this->getEntityParam('откуда');}
	/** @return string */
	public function getInFormDestination() {return $this->getEntityParam('куда');}
	
	/** @return Df_Localization_Model_Morpher_Response */
	public function getPlural() {
		if (!isset($this->{__METHOD__})) {
			df_assert($this->isSingular());
			$this->{__METHOD__} =
				self::i($this->descendS('множественное/И'), $this->e()->descendO('множественное'))
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getWhere() {return $this->getEntityParam('где');}
	/** @return bool */
	public function isGenderFeminine() {return 'Женский' === $this->getGenderAsText();}
	/** @return bool */
	public function isGenderMasculine() {return 'Мужской' === $this->getGenderAsText();}
	/** @return bool */
	public function isGenderNeuter() {return 'Средний' === $this->getGenderAsText();}

	/** @return bool */
	public function isPlural() {return !$this->isSingular();}
	
	/** @return bool */
	public function isSingular() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = (false !== mb_strpos($this->e()->asXML(), 'множественное'));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return bool
	 */
	public function isValid() {return !$this->getErrorMessage();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__CASE_NOMINATIVE, self::V_STRING_NE);
	}
	const _CLASS = __CLASS__;
	const P__CASE_NOMINATIVE = 'case_nominative';
	/**
	 * @static
	 * @param string $caseNominative
	 * @param Df_Varien_Simplexml_Element|string $xml
	 * @return Df_Localization_Model_Morpher_Response
	 */
	public static function i($caseNominative, $xml) {return new self(array(
		self::P__CASE_NOMINATIVE => $caseNominative, self::P__SIMPLE_XML => $xml
	));}
}