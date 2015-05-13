<?php
class Df_Core_Block_Element_Style extends Df_Core_Block_Element {
	/** @return string */
	public function getRulesAsText() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = implode("\n\n", df_trim($this->getSelectors()->walk('toHtml')));
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Core_Model_Style_Selector_Collection */
	public function getSelectors() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Core_Model_Style_Selector_Collection::i();
		}
		return $this->{__METHOD__};
	}

	/**
	 * Если данный метод вернёт true, то система не будет рисовать данный блок.
	 * @override
	 * @return bool
	 */
	protected function isBlockEmpty() {return !$this->getSelectors()->count();}

	/**
	 * @override
	 * @return bool
	 */
	protected function needCaching() {return true;}

	/**
	 * @override
	 * @return bool
	 */
	protected function needCachingPerRequestAction() {return true;}

	const _CLASS = __CLASS__;
	/**
	 * @param string $cacheKeySuffix
	 * @return Df_Core_Block_Element_Style
	 */
	public static function i($cacheKeySuffix) {
		return df_block(new self(array(self::P__CACHE_KEY_SUFFIX => $cacheKeySuffix)));
	}
}