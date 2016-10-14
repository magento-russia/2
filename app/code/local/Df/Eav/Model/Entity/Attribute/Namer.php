<?php
class Df_Eav_Model_Entity_Attribute_Namer extends Df_Core_Model {
	/** @return string */
	public function getResult() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = '';
			/** @var int $counter */
			$attempt = 1;
			/** @var Df_Eav_Model_Entity_Attribute $attribute */
			$attribute = Df_Eav_Model_Entity_Attribute::i();
			while (true) {
				$result = $this->getResultByAttempt($attempt);
				$attribute->loadByCode($this->getEntityTypeId(), $result);
				if (0 === df_nat0($attribute->getId())) {
					break;
				}
				$attribute->setData(array());
				$attempt++;
			};
			df_result_string_not_empty($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $name
	 * @return string
	 */
	private function adjust($name) {
		df_param_string_not_empty($name, 0);
		/** @var string $result */
		$result = str_replace('-', '_', df_output()->transliterate(df_trim($name)));
		df_result_string_not_empty($result);
		return $result;
	}

	/** @return int */
	private function getEntityTypeId() {return $this->cfg(self::P__ENTITY_TYPE_ID, rm_eav_id_product());}

	/** @return string */
	private function getNameDesired() {return $this->cfg(self::P__NAME_DESIRED);}

	/** @return string */
	private function getNameDesiredAdjusted() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =  $this->adjust($this->getNameDesired());
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return string[] */
	private function getPrefixes() {return $this->cfg(self::P__PREFIXES, array());}

	/** @return string[] */
	private function getPrefixesAdjusted() {
		if (!isset($this->{__METHOD__})) {
			/** @uses adjust() */
			$this->{__METHOD__} = array_filter(array_map(array($this, 'adjust'), $this->getPrefixes()));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param int $attempt
	 * @return string
	 */
	private function getResultByAttempt($attempt) {
		df_param_integer($attempt, 1);
		/** @var string $result */
		$result =
			substr(
				implode('__', array_merge(
					$this->getPrefixesAdjusted()
					, array($this->getNameDesiredAdjusted())
				))
				,0
				,(1 === $attempt)
				?
					Mage_Eav_Model_Entity_Attribute::ATTRIBUTE_CODE_MAX_LENGTH
				:
						Mage_Eav_Model_Entity_Attribute::ATTRIBUTE_CODE_MAX_LENGTH
					-
						(1 + mb_strlen(df_string($attempt)))
			)
		;
		if (1 < $attempt) {
			$result = implode('_', array($result, $attempt));
		}
		return $result;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__ENTITY_TYPE_ID, DF_V_INT, false)
			->_prop(self::P__NAME_DESIRED, DF_V_STRING_NE)
			->_prop(self::P__PREFIXES, DF_V_ARRAY, false)
		;
	}
	const P__ENTITY_TYPE_ID = 'entity_type_id';
	const P__NAME_DESIRED = 'name_desired';
	const P__PREFIXES = 'prefixes';
	/**
	 * @param string $nameDesired
	 * @param string[] $prefixes [optional]
	 * @param int $entityTypeId [optional]
	 * @return Df_Eav_Model_Entity_Attribute_Namer
	 */
	public static function i($nameDesired, array $prefixes = array(), $entityTypeId = null) {
		return new self(array(
			self::P__NAME_DESIRED => $nameDesired
			, self::P__PREFIXES => $prefixes
			, self::P__ENTITY_TYPE_ID => $entityTypeId
		));
	}
}