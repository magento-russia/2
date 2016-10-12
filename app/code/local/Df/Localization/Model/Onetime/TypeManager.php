<?php
class Df_Localization_Model_Onetime_TypeManager extends Df_Core_Model {
	/**
	 * @param string $type
	 * @return string
	 */
	public function getClassSuffixByType($type) {
		df_param_string_not_empty($type, 0);
		if (!isset($this->{__METHOD__}[$type])) {
			/**
			 * attribute_set => AttributeSet
			 * em.megamenupro => Em_Megamenupro
			 *
			 * cat.dog_rat => Cat_DogRat
			 * шаг 1: cat.dog_rat => cat.DogRat
			 * шаг 2: cat.DogRat => Cat_DogRat
			 *
			 * В качестве исходного разделителя используем точку,
			 * потому что слеш недопустим в названии тега,
			 * а название типа используется в названии тега условий:
				<rule>
					<conditions>
						<type>em.megamenupro</type>
						<em.megamenupro>
							<title>Sample Horizontal Menu</title>
						</em.megamenupro>
					</conditions>
					<actions>
						<new_title>Пример горизонтального меню</new_title>
					</actions>
				</rule>
			 */
			$this->{__METHOD__}[$type] =
				// шаг 2: cat.DogRat => Cat_DogRat
				uc_words(
					// шаг 1: cat.dog_rat => cat.DogRat
					uc_words($type, $destSep='', $srcSep='_')
					, $destSep = '_'
					, $srcSep='.'
				)
			;
		}
		return $this->{__METHOD__}[$type];
	}

	/**
	 * @param string $type
	 * @return Df_Localization_Model_Onetime_Type
	 */
	public function getType($type) {
		df_param_string_not_empty($type, 0);
		if (!isset($this->_types[$type])) {
			/** @var string $resultClass */
			$resultClass =
				'Df_Localization_Model_Onetime_Type_' . $this->getClassSuffixByType($type)
			;
			/** @var Df_Localization_Model_Onetime_Type $result */
			$result = new $resultClass;
			df_assert($result instanceof Df_Localization_Model_Onetime_Type);
			$this->_types[$type] = $result;
		}
		return $this->_types[$type];
	}
	/** @var array(string => Df_Localization_Model_Onetime_Type) */
	private $_types = array();

	/** @return void */
	public function saveModifiedMagentoEntities() {
		foreach ($this->_types as $type) {
			/** @var Df_Localization_Model_Onetime_Type $type */
			$type->saveModifiedEntities();
		}
	}

	/** @return Df_Localization_Model_Onetime_TypeManager */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}