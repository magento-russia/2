<?php
class Df_Core_Helper_Js extends Mage_Core_Helper_Js {
	/**
	 * @override
	 * @return string
	 */
	public function getTranslatorScript() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			/** @var string $cacheKey */
			$cacheKey = $this->getCache()->makeKey(__METHOD__, df_locale());
			$result = $this->getCache()->loadData($cacheKey);
			if (!$result) {
				$result = parent::getTranslatorScript();
				$this->getCache()->saveData($cacheKey, $result);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Core_Model_Cache */
	private function getCache() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Core_Model_Cache::i('translate', true, Mage_Core_Model_Translate::CACHE_TAG)
			;
		}
		return $this->{__METHOD__};
	}
}