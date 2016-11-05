<?php
class Df_Reports_Helper_GroupResultsByWeek extends Mage_Core_Helper_Abstract {
	/** @return bool */
	public function isSelectedInFilter() {
		if (!isset($this->{__METHOD__})) {
			/** @var bool $result */
			$this->{__METHOD__} = ('week' === dfa($this->getFilterAsArray(), 'period_type'));
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => string) */
	private function getFilterAsArray() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => string) $result */
			$result = [];
			/** @var string|null $f$filterAsStringilter */
			$filterAsString = df_request('filter');
			if (!is_null($filterAsString)) {
				df_assert_string($filterAsString);
				/** @var array $result */
				$result = df_mage()->adminhtml()->helper()->prepareFilterString($filterAsString);
			}
			df_result_array($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}