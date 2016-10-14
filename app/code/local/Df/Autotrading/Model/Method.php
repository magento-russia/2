<?php
class Df_Autotrading_Model_Method extends Df_Shipping_Model_Method_CollectedManually {
	/**
	 * @override
	 * @return string
	 */
	public function getMethod() {return 'standard';}

	/**
	 * @override
	 * @return void
	 * @throws Exception
	 */
	protected function checkApplicability() {
		parent::checkApplicability();
		$this
			/**
			 * Странно, что валидатор @uses Df_Shipping_Model_Method::checkCountryOriginIsRussia()
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

	/** @used-by Df_Autotrading_Model_Collector::getMethods() */
	const _C = __CLASS__;
}