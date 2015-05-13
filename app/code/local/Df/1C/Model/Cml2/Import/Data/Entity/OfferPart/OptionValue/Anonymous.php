<?php
class Df_1C_Model_Cml2_Import_Data_Entity_OfferPart_OptionValue_Anonymous
	extends Df_1C_Model_Cml2_Import_Data_Entity_OfferPart_OptionValue {
	/**
	 * @override
	 * @return string
	 */
	public function getName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_sprintf('Вариант [%s]', $this->getEntityProduct()->getAppliedTypeName());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	public function getValue() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_trim(
					strtr(
						$this->getOffer()->getName()
						, array($this->getEntityProduct()->getName() => '')
					)
					,'()'
				)
			;
			if (!$this->{__METHOD__}) {
				df_error(
					'Система не смогла извлечь значение настраиваемой опции'
					. ' из названия товарного предложения «%s».'
					. "\r\nНазвание настраиваемого товара: «%s»."
					, $this->getOffer()->getName()
					, $this->getEntityProduct()->getName()
				);
			}
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getAttributeCodeGenerated() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_h()->_1c()->generateAttributeCode(
					'Вариант'
					// Намеренно поставил второй параметр ($this->getEntityProduct()->getAppliedTypeName()),
					// потому что счёл ненужным в данном случае
					// использовать приставку для системных имён товарных свойств,
					// потому что основная часть («Вариант») несёт мало полезной информации.
					, $this->getEntityProduct()->getAppliedTypeName()
				)
			;
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param Df_1C_Model_Cml2_Import_Data_Entity_Offer $offer
	 * @return Df_1C_Model_Cml2_Import_Data_Entity_OfferPart_OptionValue
	 */
	public static function i(Df_1C_Model_Cml2_Import_Data_Entity_Offer $offer) {
		return new self(array(self::P__OFFER => $offer));
	}
}