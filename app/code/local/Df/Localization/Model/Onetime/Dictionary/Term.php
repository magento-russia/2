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
	 * 2015-08-23
	 * Здесь допустим как двухсторонний, так и односторонний лайк (только справа или только слева).
	 * @used-by Df_Localization_Model_Onetime_Processor_Db_Column::isItLike()
	 * @return bool
	 */
	public function isItLike() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				rm_starts_with($this->getFrom(), '%') || rm_ends_with($this->getFrom(), '%')
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Обратите внимание, что символ процента должен стоять с обеих сторон фразы.
	 * Тогда замену проще запрограммировать,
	 * и для большинства практических ситуаций этого достаточно).
	 * @used-by Df_Localization_Model_Onetime_Processor_Entity::translate()
	 * @return bool
	 */
	public function isItLike2() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				rm_starts_with($this->getFrom(), '%') && rm_ends_with($this->getFrom(), '%')
			;
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