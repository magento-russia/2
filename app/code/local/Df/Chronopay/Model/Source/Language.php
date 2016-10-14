<?php
class Df_Chronopay_Model_Source_Language {
	/** @return array(array(string => string|int)) */
	public function toOptionArray() {
		return rm_map_to_options(array(
			'EN' => 'English', 'RU' => 'Russian', 'NL' => 'Dutch' ,'DE' => 'German'
		), $this);
	}
}