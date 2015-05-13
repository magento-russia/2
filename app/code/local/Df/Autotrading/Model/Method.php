<?php
class Df_Autotrading_Model_Method extends Df_Shipping_Model_Method_CollectedManually {
	/**
	 * @override
	 * @return string
	 */
	public function getMethod() {return 'standard';}

	/**
	 * @override
	 * @return bool
	 * @throws Exception
	 */
	public function isApplicable() {
		/** @var bool $result */
		$result = parent::isApplicable();
		if ($result) {
			try {
				$this
					/**
					 * Странно, что валидатор @see Df_Shipping_Model_Method::checkCountryOriginIsRussia()
					 * отсутствовал здесь ранее: судя по калькулятору на официальном сайте,
					 * Автотрейдинг отправляет грузы только из России.
					 */
					->checkCountryOriginIsRussia()
					->checkCityDestinationIsNotEmpty()
					->checkCityOriginIsNotEmpty()
					->checkOriginAndDestinationCitiesAreDifferent()
					/**
					 * 2014-08-24
					 * Заметил, что калькулятор на сайте Автотрейдинга и их API
					 * перестали рассчитывать доставку за рубеж,
					 * хотя описание услуги международной доставки на сайте Автотрейдинга осталось.
					 */
					->checkCountryDestinationIsRussia()
				;
			}
			catch(Exception $e) {
				if ($this->needDisplayDiagnosticMessages()) {throw $e;} else {$result = false;}
			}
		}
		return $result;
	}

	const _CLASS = __CLASS__;
}