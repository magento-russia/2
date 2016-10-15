<?php
class Df_Catalog_Model_Config_Backend_Conditions extends Df_Admin_Config_Backend {
	/**
	 * @overide
	 * @return Df_Catalog_Model_Config_Backend_Conditions
	 */
	protected function _beforeSave() {
		try {
			if ($this->validate()) {
				$this->getRule()->loadPost(array(
					'conditions' => dfa(dfa($this->getPost(), 'rule'), 'conditions')
					,'website_ids' => $this->getWebsiteIds()
				));
				$this->getRule()->setDataChanges(true);
				$this->getRule()->save();
				$this->setValue($this->getRule()->getId());
			}
		}
		catch (Exception $e) {
			df_notify_exception($e);
			df_exception_to_session($e);
		}
		parent::_beforeSave();
		return $this;
	}

	/** @return string[] */
	private function getPost() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->filterDates(
				Mage::app()->getRequest()->getPost(), array('from_date', 'to_date')
			);
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
				df_nat($result->getId());
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
			$this->{__METHOD__} = df_nat0($this->getOldValue());
		}
		return $this->{__METHOD__};
	}
	
	/**
	 * Этот метод был скопирован в ноябре 2012 года из
	 * @see Mage_Core_Controller_Varien_Action::_filterDates()
	 * @param array(string => string|mixed) $array
	 * @param string[] $dateFields
	 * @return array(string => string)
	 */
	private function filterDates(array $array, array $dateFields) {
		/** @var Zend_Filter_LocalizedToNormalized $filterInput */
		$filterInput = new Zend_Filter_LocalizedToNormalized(array('date_format' =>
			Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
		));
		/** @var Zend_Filter_NormalizedToLocalized $filterInternal */
		$filterInternal = new Zend_Filter_NormalizedToLocalized(array('date_format' =>
			Varien_Date::DATE_INTERNAL_FORMAT
		));
		foreach ($dateFields as $dateField) {
			/** @var string $dateField */
			if (array_key_exists($dateField, $array) && !empty($dateField)) {
				$array[$dateField] = $filterInternal->filter($filterInput->filter($array[$dateField]));
			}
		}
		return $array;
	}		

	/** @return bool */
	private function validate() {
		/** @var bool|array $validateResult */
		$validateResult = $this->getRule()->validateData(new Varien_Object($this->getPost()));
		if (true !== $validateResult) {
			/** @uses Mage_Core_Model_Session_Abstract::addError() */
			array_map(array(df_session(), 'addError'), $validateResult);
		}
		return (true === $validateResult);
	}
}