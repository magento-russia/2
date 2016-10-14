<?php
class Df_Phpquery_Lib extends Df_Core_Lib {
	/**
	 * @override
	 * @return int
	 */
	protected function getIncompatibleErrorLevels() {return E_NOTICE;}

	/**
	 * @used-by df_pq()
	 * @used-by df_pq_options()
	 * @return Df_Phpquery_Lib
	 */
	public static function s() {return self::load(__CLASS__);}
}