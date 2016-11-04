<?php
namespace Df\C1\Cml2\Import\Data\Entity\OfferPart\OptionValue;
class Anonymous extends \Df\C1\Cml2\Import\Data\Entity\OfferPart\OptionValue {
	/**
	 * @override
	 * @return string
	 */
	public function getName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = sprintf('Вариант [%s]', $this->getEntityProduct()->getAppliedTypeName());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	public function getValue() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_trim(
				str_replace($this->getEntityProduct()->getName(), '', $this->getOffer()->getName())
				, '()'
			);
			if (!$this->{__METHOD__}) {
				df_error(
					'Система не смогла извлечь значение настраиваемой опции'
					. ' из названия товарного предложения «%s».'
					. "\nНазвание настраиваемого товара: «%s»."
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
			$this->{__METHOD__} = df_c1()->generateAttributeCode(
				'Вариант'
				// Намеренно поставил второй параметр ($this->getEntityProduct()->getAppliedTypeName()),
				// потому что счёл ненужным в данном случае
				// использовать приставку для системных имён товарных свойств,
				// потому что основная часть («Вариант») несёт мало полезной информации.
				, $this->getEntityProduct()->getAppliedTypeName()
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @static
	 * @param \Df\C1\Cml2\Import\Data\Entity\Offer $offer
	 * @return \Df\C1\Cml2\Import\Data\Entity\OfferPart\OptionValue
	 */
	public static function i(\Df\C1\Cml2\Import\Data\Entity\Offer $offer) {
		return new self(array(self::P__OFFER => $offer));
	}
}