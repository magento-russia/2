<?php
class Df_Localization_Morpher extends Df_Core_Model {
	/**
	 * @param string $word
	 * @return Df_Localization_Morpher_Response
	 */
	public function getResponse($word) {
		if (!isset($this->{__METHOD__}[$word])) {
			df_param_string_not_empty($word, 0);
			/** @var Df_Localization_Morpher_Response $result */
			$result = Df_Localization_Morpher_Response::i($word, $this->getResponseAsText($word));
			if (!$result->isValid()) {
				df_error(
					'При вычислении склонений слова «%s» произошёл сбой: «%s».'
					,$word
					,$result->getErrorMessage()
				);
			}
			$this->{__METHOD__}[$word] = $result;
		}
		return $this->{__METHOD__}[$word];
	}

	/**
	 * @param string $word
	 * @return Df_Localization_Morpher_Response|null
	 */
	public function getResponseSilent($word) {
		/** @var Df_Localization_Morpher_Response|null $result */
		$result = null;
		try {
			$result = $this->getResponse($word);
		}
		catch (Exception $e) {}
		return $result;
	}

	/** @return Df_Core_Model_Cache */
	private function getCache() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Core_Model_Cache::i(null, true);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $word
	 * @return string
	 */
	private function getResponseAsText($word) {
		if (!isset($this->{__METHOD__}[$word])) {
			df_param_string_not_empty($word, 0);
			/** @var string $cacheKey */
			$cacheKey = $this->getCache()->makeKey(__METHOD__, $word);
			/** @var string $result */
			$result = $this->getCache()->loadData($cacheKey);
			if (false === $result) {
				$result = Df_Localization_Morpher_Request::i($word)->getResponse();
				$this->getCache()->saveData($cacheKey, $result);
			}
			df_result_string_not_empty($result);
			$this->{__METHOD__}[$word] = $result;
		}
		return $this->{__METHOD__}[$word];
	}



	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}