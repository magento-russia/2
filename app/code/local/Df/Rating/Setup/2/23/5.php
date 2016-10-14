<?php
class Df_Rating_Setup_2_23_5 extends Df_Core_Setup {
	/**
	 * Переводим англоязычные назания стандартных оценочных критериев: «Price», «Quality», «Value».
	 * @override
	 * @see Df_Core_Setup::_process()
	 * @used-by Df_Core_Setup::process()
	 * @return void
	 */
	protected function _process() {
		foreach (Df_Rating_Model_Rating::c() as $rating) {
			/** @var Df_Rating_Model_Rating $rating */
			$rating->setRatingCode($this->translate($rating->getRatingCode()));
			$rating->save();
		}
	}

	/**
	 * @param string $ratingCode
	 * @return string
	 */
	private function translate($ratingCode) {
		df_param_string_not_empty($ratingCode, 0);
		return
			dfa(
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
}