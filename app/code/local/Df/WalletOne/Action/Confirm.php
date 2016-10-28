<?php
namespace Df\WalletOne\Action;
use Df\WalletOne\Request\SignatureGenerator as G;
class Confirm extends \Df\Payment\Action\Confirm {
	/**
	 * @override
	 * @return void
	 */
	protected function alternativeProcessWithoutInvoicing() {
		$this->order()->comment('Покупатель отказался от оплаты');
	}

	/**
	 * Использовать getConst нельзя из-за рекурсии.
	 * @override
	 * @return string
	 */
	protected function rkOII() {return 'WMI_PAYMENT_NO';}

	/**
	 * @override
	 * @param \Exception $e
	 * @return string
	 */
	protected function responseTextForError(\Exception $e) {
		return 'WMI_RESULT=CANCEL&WMI_DESCRIPTION=' . urlencode(df_ets($e));
	}

	/**
	 * @override
	 * @return string
	 */
	protected function responseTextForSuccess() {return 'WMI_RESULT=OK';}

	/**
	 * @override
	 * @return string
	 */
	protected function signatureOwn() {
		return $this->getSignatureGenerator()->getSignature();
	}

	/**
	 * @override
	 * @return bool
	 */
	protected function needInvoice() {
		return 'accepted' === mb_strtolower($this->rState());
	}

	/**
	 * @override
	 * @return \Df\Payment\Action\Confirm
	 * @throws \Mage_Core_Exception
	 */
	protected function processOrderCanNotInvoice() {
		// Единая Касса любит присылать повторные оповещения об оплате
		$this->order()->comment('Единая Касса повторно прислала оповещение об оплате');
		$this->response()->setBody($this->responseTextForSuccess());
		return $this;
	}

	/** @return G */
	private function getSignatureGenerator() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = G::i(array(
				G::P__ENCRYPTION_KEY => $this->configS()->getResponsePassword()
				,G::P__SIGNATURE_PARAMS =>
					array_diff_key($this->params(), [$this->rkSignature() => null])
			));
		}
		return $this->{__METHOD__};
	}
}