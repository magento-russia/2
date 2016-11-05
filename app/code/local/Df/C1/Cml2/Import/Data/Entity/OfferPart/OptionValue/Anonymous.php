<?php
namespace Df\C1\Cml2\Import\Data\Entity\OfferPart\OptionValue;
use Df\C1\Cml2\Import\Data\Entity\Offer;
class Anonymous extends \Df\C1\Cml2\Import\Data\Entity\OfferPart\OptionValue {
	/**
	 * @override
	 * @return string
	 */
	public function getName() {return dfc($this, function() {return
		"Вариант [{$this->getEntityProduct()->getAppliedTypeName()}]"
	;});}

	/**
	 * @override
	 * @return string
	 */
	public function getValue() {return dfc($this, function() {
		/** @var string $result */
		$result = df_trim(
			str_replace($this->getEntityProduct()->getName(), '', $this->getOffer()->getName())
			, '()'
		);
		if (!$result) {
			df_error(
				'Система не смогла извлечь значение настраиваемой опции'
				. ' из названия товарного предложения «%s».'
				. "\nНазвание настраиваемого товара: «%s»."
				, $this->getOffer()->getName()
				, $this->getEntityProduct()->getName()
			);
		}
		return $result;
	});}

	/**
	 * @override
	 * @return string
	 */
	protected function getAttributeCodeGenerated() {return dfc($this, function() {return
		df_c1()->generateAttributeCode(
			'Вариант'
			// Намеренно поставил второй параметр ($this->getEntityProduct()->getAppliedTypeName()),
			// потому что счёл ненужным в данном случае
			// использовать приставку для системных имён товарных свойств,
			// потому что основная часть («Вариант») несёт мало полезной информации.
			,$this->getEntityProduct()->getAppliedTypeName()
		)
	;});}

	/**
	 * @static
	 * @param Offer $offer
	 * @return self
	 */
	public static function i(Offer $offer) {return new self([self::P__OFFER => $offer]);}
}