<?php
class Df_YandexMarket_Model_Category_Adviser extends Df_Core_Model_DestructableSingleton {
	/**
	 * @param string $piece
	 * @return string[]
	 */
	public function getSuggestions($piece) {
		if (!isset($this->{__METHOD__}[$piece])) {
			$this->{__METHOD__}[$piece] =
				Df_YandexMarket_Model_Category_Adviser_Case::i($piece)->getSuggestions()
			;
			$this->markCachedPropertyAsModified(__METHOD__);
		}
		return $this->{__METHOD__}[$piece];
	}

	/**
	 * @override
	 * @return int|null
	 */
	protected function getCacheLifetime() {return 86400 * 7;}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getPropertiesToCache() {return self::m(__CLASS__, 'getSuggestions');}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getPropertiesToCacheSimple() {return $this->getPropertiesToCache();}

	/** @return Df_YandexMarket_Model_Category_Adviser */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}

