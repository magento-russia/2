<?php
class Df_Localization_Model_Onetime_Dictionary_Term extends Df_Core_Model_SimpleXml_Parser_Entity {
	/**
	 * @override
	 * @return string
	 */
	public function getId() {return $this->getFrom();}

	/** @return string|null */
	public function getFrom() {return $this->getEntityParam('from');}

	/** @return string */
	public function getFromForLike() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_trim($this->getFrom(), '%');
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getFromNormalized() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_normalize($this->getFrom());
		}
		return $this->{__METHOD__};
	}

	/** @return string|null */
	public function getTo() {return $this->getEntityParam('to');}

	/**
	 * Обратите внимание, что символ процента должен стоять с обеих сторон фразы.
	 * @return bool
	 */
	public function isItLike() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_text()->isLike($this->getFrom());
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function isItRegEx() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_text()->isRegex($this->getFrom());
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function needFromBeEmpty() {return '{empty}' === $this->getFrom();}

	/** Используется из @see Df_Localization_Model_Onetime_Dictionary_Terms::getItemClass() */
	const _CLASS = __CLASS__;

	/**
	 * @static
	 * @param Df_Varien_Simplexml_Element|string $simpleXml
	 * @return Df_Localization_Model_Onetime_Dictionary_Term
	 */
	public static function i($simpleXml) {return self::_c($simpleXml, __CLASS__);}
}