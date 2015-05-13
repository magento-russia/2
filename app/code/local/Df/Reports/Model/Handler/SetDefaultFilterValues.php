<?php
/**
 * @method Df_Core_Model_Event_Adminhtml_Block_HtmlBefore getEvent()
 */
class Df_Reports_Model_Handler_SetDefaultFilterValues extends Df_Core_Model_Handler {
	/**
	 * Метод-обработчик события
	 * @override
	 * @return void
	 */
	public function handle() {
		$this->setEndDateToYesterday();
		if ($this->getPeriodDuration()) {
			$this->setStartDate();
		}
	}

	/**
	 * Класс события (для валидации события)
	 * @override
	 * @return string
	 */
	protected function getEventClass() {
		return Df_Core_Model_Event_Adminhtml_Block_HtmlBefore::_CLASS;
	}

	/** @return Mage_Adminhtml_Block_Report_Filter_Form */
	private function getBlockAsReportFilterForm() {
		return $this->getEvent()->getBlock();
	}

	/** @return array(string => int|string)|null */
	private function getPeriodDuration() {
		if (!isset($this->{__METHOD__})) {
			/** @var array|null $result */
			$result = null;
			if (
					Df_Reports_Model_System_Config_Source_Duration::UNDEFINED
				!==
					df_cfg()->reports()->common()->getPeriodDuration()
			) {
				/** @var Df_Reports_Model_System_Config_Source_Duration $configDuration */
				$configDuration = Df_Reports_Model_System_Config_Source_Duration::i();
				foreach ($configDuration->toOptionArray() as $option) {
					/** @var array $option */
					if (
							df_cfg()->reports()->common()->getPeriodDuration()
						===
							df_a($option, Df_Admin_Model_Config_Source::OPTION_KEY__VALUE)
					)  {
						/** @var array(string => int|string) $duration */
						$result =
							df_a(
								$option
								,Df_Reports_Model_System_Config_Source_Duration
									::OPTION_PARAM__DURATION
							)
						;
						df_assert_array($result);
						break;
					}
				}
			}
			if (!is_null($result)) {
				df_assert_array($result);
			}
			$this->{__METHOD__} = rm_n_set($result);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return Df_Reports_Model_Handler_SetDefaultFilterValues */
	private function setEndDateToYesterday() {
		/** @var Varien_Data_Form_Element_Date|null $elementEndDate */
		$elementEndDate =
			$this->getBlockAsReportFilterForm()->getForm()->getElement(self::FORM_ELEMENT__TO)
		;
		if ($elementEndDate && !$elementEndDate->getValueInstance()) {
			$elementEndDate->setValue(df()->date()->cleanTime(df()->date()->yesterday()));
		}
		return $this;
	}

	/** @return Df_Reports_Model_Handler_SetDefaultFilterValues */
	private function setStartDate() {
		/** @var Varien_Data_Form_Element_Date|null $elementStartDate */
		$elementStartDate =
			$this->getBlockAsReportFilterForm()->getForm()->getElement(self::FORM_ELEMENT__FROM)
		;
		if ($elementStartDate && !$elementStartDate->getValueInstance()) {
			/** @var Varien_Data_Form_Element_Date|null $elementEndDate */
			$elementEndDate =
				$this->getBlockAsReportFilterForm()->getForm()->getElement(self::FORM_ELEMENT__TO)
			;
			if ($elementEndDate) {
				/** @var Zend_Date|null $endDate */
				$endDate = $elementEndDate->getValueInstance();
				if ($endDate) {
					/** @var Zend_Date $startDate */
					$startDate = new Zend_Date($endDate);
					/**
					 * Zend_Date::sub() возвращает число в виде строки для Magento CE 1.4.0.1
					 * и объект класса Zend_Date для более современных версий Magento
					 */
					$startDate->sub(
						df_a(
							$this->getPeriodDuration()
							,Df_Reports_Model_System_Config_Source_Duration
								::OPTION_PARAM__DURATION__VALUE
						)
						,df_a(
							$this->getPeriodDuration()
							,Df_Reports_Model_System_Config_Source_Duration
								::OPTION_PARAM__DURATION__DATEPART
						)
					);
					$elementStartDate->setValue($startDate);
				}
			}
		}
		return $this;
	}

	const _CLASS = __CLASS__;
	const FORM_ELEMENT__FROM = 'from';
	const FORM_ELEMENT__TO = 'to';
}