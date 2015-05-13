<?php
class Df_UkrPoshta_Model_Request_Rate extends Df_Shipping_Model_Request {
	/** @return float */
	public function getResult() {
		if (!isset($this->{__METHOD__})) {
			/** @var float $result */
			$result = null;
			$this->responseFailureDetect();
			foreach ($this->response()->pq('table.result > tr') as $domRow) {
				/** @var DOMNode $domRow */
				/** @var phpQueryObject $pqRow */
				$pqRow = df_pq($domRow);
				/** @var string $rowTitle */
				$rowTitle = df_trim(pq('td.leftResultBold', $pqRow)->text());
				df_assert_string($rowTitle);
				if ('Всього:' === $rowTitle) {
					/** @var phpQueryObject $pqCellValue */
					$pqCellValue = df_pq('td.rightResultBold', $pqRow);
					/** @var string $cellValue */
					$cellValue = $pqCellValue->html();
					df_assert_string_not_empty($cellValue);
					/** @var string $rateAsText */
					$rateAsText = strtr(df_trim($cellValue), array(',' => '.', ' грн.' => ''));
					$result = rm_float($rateAsText);
					break;
				}
			}
			df_result_float($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getHeaders() {
		return array_merge(parent::getHeaders(), array(
			'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
			,'Accept-Encoding' => 'gzip, deflate'
			,'Accept-Language' => 'en-us,en;q=0.5'
			,'Connection' => 'keep-alive'
			,'Host' => 'services.ukrposhta.com'
			,'User-Agent' => Df_Core_Const::FAKE_USER_AGENT
		));
	}
	/**
	 * @override
	 * @return string
	 */
	protected function getQueryHost() {return 'services.ukrposhta.com';}
	/**
	 * @override
	 * @return string
	 */
	protected function getQueryPath() {return '/CalcUtil/Calculate.aspx';}
	/**
	 * @override
	 * @return Df_Shipping_Model_Request
	 */
	protected function responseFailureDetectInternal() {
		if ($this->response()->contains('Runtime Error')) {
			/** @var string $errorMessage */
			$errorMessage = df_trim($this->response()->pq('h1')->text());
			$this->responseFailureHandle(
				$errorMessage ? $errorMessage : self::T__ERROR_MESSAGE__DEFAULT
			);
		}
		return $this;
	}

	const _CLASS = __CLASS__;
}