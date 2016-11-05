<?php
class Df_Admin_Model_Form_Element extends Df_Core_Model {
	/** @return string */
	public function getCheckboxLabel() {
		return
			$this->e()->getCanUseWebsiteValue()
			? df_mage()->adminhtml()->__('Use Website')
			: (
				$this->e()->getCanUseDefaultValue()
				? df_mage()->adminhtml()->__('Use Default')
				: ''
			)
		;
	}

	/** @return string */
	public function getComment() {return df_nts($this->e()->getComment());}

	/** @return string */
	public function getDefaultText() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->getOptions() ? $this->getDefaultValueFromOptions() : $this->e()->getDefaultValue()
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getHint() {return $this->e()->getHint();}

	/** @return bool */
	public function getInherit() {return !!$this->e()->getInherit();}

	/** @return string */
	public function getNamePrefix() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = preg_replace('#\[value\](\[\])?$#', '', $this->e()->getName());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getScopeLabel() {return $this->e()->getScope() ? $this->e()->getScopeLabel() : '';}

	/** @return Mage_Core_Model_Config_Element */
	protected function getFieldConfig() {return $this->e()->getFieldConfig();}

	/** @return string */
	private function getDefaultValueFromOptions() {
		$defTextArr = [];
		foreach ($this->getOptions() as $k=>$v) {
			/** @var string $k */
			/** @var array $v */
			if ($this->isMultiple()) {
				if (is_array($v['value']) && in_array($k, $v['value'])) {
					$defTextArr[]= $v['label'];
				}
			}
			else if ($v['value'] === $this->e()->getDefaultValue()) {
				$defTextArr[]= $v['label'];
				break;
			}
		}
		return df_csv_pretty($defTextArr);
	}

	/** @return array */
	private function getOptions() {return df_nta($this->e()->getValues());}

	/** @return string */
	public function getHtml() {return $this->e()->getElementHtml();}

	/** @return string */
	public function getId() {return $this->e()->getHtmlId();}

	/** @return string */
	public function getLabel() {return df_nts($this->e()->getLabel());}

	/** @return bool */
	public function needToAddInheritBox() {
		return $this->e()->getCanUseWebsiteValue() && $this->e()->getCanUseDefaultValue();
	}

	/**
	 * 2015-03-08
	 * Обратите внимание на технику:
	 * добавляем в описание типа «|Df_Varien_Data_Form_Element_Abstract»,
	 * чтобы среда разработки знала о псевдометодах типа $this->e()->getCanUseWebsiteValue(),
	 * которые не описаны в классе @see Varien_Data_Form_Element_Abstract (при этом доступны там),
	 * но описаны специально для среды разработки в классе @see Df_Varien_Data_Form_Element_Abstract
	 * @return Varien_Data_Form_Element_Abstract|Df_Varien_Data_Form_Element_Abstract
	 */
	private function e() {
		if (!isset($this->{__METHOD__})) {
			/** @var Varien_Data_Form_Element_Abstract|Df_Varien_Data_Form_Element_Abstract $r */
			$r = $this->cfg(self::$P__E);
			if ($r->getCanUseWebsiteValue() && $r->getCanUseDefaultValue() && $r->getInherit()) {
				$r->setDisabled(true);
			}
			$this->{__METHOD__} = $r;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function isMultiple() {return 'multiple' === $this->e()->getExtType();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__E, 'Varien_Data_Form_Element_Abstract');
	}
	/** @var string */
	private static $P__E = 'e';

	/**
	 * @used-by Df_Admin_Block_Field::render()
	 * @param Varien_Data_Form_Element_Abstract $e
	 * @return Df_Admin_Model_Form_Element
	 */
	public static function i(Varien_Data_Form_Element_Abstract $e) {
		return new self(array(self::$P__E => $e));
	}
}