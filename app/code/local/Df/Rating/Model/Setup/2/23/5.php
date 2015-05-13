<?php
class Df_Rating_Model_Setup_2_23_5 extends Df_Core_Model_Setup {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		// Переводим англоязычные назания стандартных оценочных критериев
		// «Price», «Quality», «Value»
		foreach (Df_Rating_Model_Rating::c() as $rating) {
			/** @var Df_Rating_Model_Rating $rating */
			$rating
				->setRatingCode($this->translate($rating->getRatingCode()))
				->save()
			;
		}
	}

	/**
	 * @param string $ratingCode
	 * @return string
	 */
	private function translate($ratingCode) {
		df_param_string_not_empty($ratingCode, 0);
		return
			df_a(
				array(
					'Price' => 'Цена'
					,'Quality' => 'Качество'
					,'Value' => 'Полезность'
				)
				,$ratingCode
				,$ratingCode
			)
		;
	}

	/** @return Df_Rating_Model_Setup_2_23_5 */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}