<?php
class Df_Psbank_ConfirmController extends Mage_Core_Controller_Front_Action {
	/**
	 * Платёжная система присылает сюда подтверждения факта выполнения
	 * всех запрашиваемых интернет-магазином у платёжной системы операций
	 * @return void
	 */
	public function indexAction() {$this->getAction()->process();}

	/** @return Df_Psbank_Model_Action_Confirm */
	private function getAction() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_model(
				$this->getActionClass(), array(Df_Psbank_Model_Action_Confirm::P__CONTROLLER => $this)
			);
			df_assert($this->{__METHOD__} instanceof Df_Psbank_Model_Action_Confirm);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getActionClass() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_a($this->getActionMap(), $this->getActionCode());
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	private function getActionCode() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|int $result */
			$result = $this->getRequest()->getParam('TRTYPE');
			if (is_null($result)) {
				df_error(
					'При обращении по данному веб-адресу надо указывать код операции параметром «TRTYPE»,'
					.' однако этот код в Вашем обращении отсутствует.'
				);
			}
			if (!ctype_digit($result)) {
				df_error('Код операции («TRTYPE») должен содержать только цифры');
			}
			$result = rm_nat0($result);
			if (!in_array($result, array_keys($this->getActionMap()))) {
				df_error('Системе непонятен код запрашиваемой операции: «%d»', $this->getActionCode());
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return array(int => string) */
	private function getActionMap() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array(
				0 => Df_Psbank_Model_Action_Confirm_Authorize::_CLASS
				,1 => Df_Psbank_Model_Action_Confirm_Payment::_CLASS
				,21 => Df_Psbank_Model_Action_Confirm_Capture::_CLASS
				,22 => Df_Psbank_Model_Action_Confirm_Void::_CLASS
			);
		}
		return $this->{__METHOD__};
	}
}