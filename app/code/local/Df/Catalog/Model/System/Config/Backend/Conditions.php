<?php
class Df_Catalog_Model_System_Config_Backend_Conditions extends Df_Admin_Model_Config_Backend {
	/**
	 * @overide
	 * @return Df_Catalog_Model_System_Config_Backend_Conditions
	 */
	protected function _beforeSave() {
		try {
			if ($this->validate()) {
				$this->getRule()->loadPost(array(
					'conditions' => df_a(df_a($this->getPost(), 'rule'), 'conditions')
					,'website_ids' => $this->getWebsiteIds()
				));
				$this->getRule()->setDataChanges(true);
				$this->getRule()->save();
				rm_nat($this->getRule()->getId());
				$this->setValue($this->getRule()->getId());
			}
		}
		catch(Exception $e) {
			df_notify_exception($e);
			rm_exception_to_session($e);
		}
		parent::_beforeSave();
		return $this;
	}

	/** @return string[] */
	private function getPost() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df()->request()->filterDates(
					rm_state()->getController()->getRequest()->getPost()
					, array('from_date', 'to_date')
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Mage_CatalogRule_Model_Rule */
	private function getRule() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_CatalogRule_Model_Rule $result */
			$result = df_model('catalogrule/rule');
			if ($this->getRuleExistingId()) {
				$result->load($this->getRuleExistingId());
				rm_nat($result->getId());
			}
			$result->addData(array(
				'name' => 'Не редактировать'
				,'description' => 'внутреннее правило, используется модулями 1C или Яндекс.Маркет'
			));
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	private function getRuleExistingId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_nat0($this->getOldValue());
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function validate() {
		/** @var bool|array $validateResult */
		$validateResult = $this->getRule()->validateData(new Varien_Object($this->getPost()));
		if (true !== $validateResult) {
			foreach ($validateResult as $errorMessage) {
				rm_session()->addError($errorMessage);
			}
		}
		return (true === $validateResult);
	}

	const _CLASS = __CLASS__;
}