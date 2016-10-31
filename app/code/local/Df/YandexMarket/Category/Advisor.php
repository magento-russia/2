<?php
namespace Df\YandexMarket\Category;
class Advisor extends \Df_Core_Model {
	/**
	 * @param string $piece
	 * @return string[]
	 */
	public function getSuggestions($piece) {
		if (!isset($this->{__METHOD__}[$piece])) {
			$this->{__METHOD__}[$piece] = Advisor\CaseT::i($piece)->getSuggestions();
			$this->markCachedPropertyAsModified(__METHOD__);
		}
		return $this->{__METHOD__}[$piece];
	}

	/**
	 * @override
	 * @see Df_Core_Model::cacheLifetime()
	 * @used-by Df_Core_Model::cacheSaveProperty()
	 * @return int|null
	 */
	protected function cacheLifetime() {return 86400 * 7;}

	/**
	 * @override
	 * @see Df_Core_Model::cachedGlobal()
	 * @return string[]
	 */
	protected function cachedGlobal() {return self::m(__CLASS__, 'getSuggestions');}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}

