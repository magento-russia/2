<?php
class Df_Core_LibLoader extends Df_Core_LibLoader_Abstract {
	/**
	 * @override
	 * @return string[]
	 */
	protected function getScriptsToInclude() {
		/** @var string $base */
		$base = 'fp' . DS;
		return array(
			$base . 'compiled'
			, $base . '1c'
			, $base . 'array'
			, $base . 'catalog'
			, $base . 'date'
			, $base . 'db'
			, $base . 'domain'
			, $base . 'filesystem'
			, $base . 'float'
			, $base . 'licensor'
			, $base . 'other'
			, $base . 'reflection'
			, $base . 'serialize'
			, $base . 'state'
			, $base . 'store'
			, $base . 'text'
			, $base . 'validation'
			, $base . 'xml'
		);
	}

	/** @return Df_Core_LibLoader */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}