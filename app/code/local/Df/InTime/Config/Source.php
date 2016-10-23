<?php
class Df_InTime_Config_Source extends Df_Core_Model {
	/**
	 * @used-by Mage_Adminhtml_Block_System_Config_Form::initFields()
	 * @used-by Df_Core_Model_Abstract::cacheLoadProperty()
	 * @used-by Df_Core_Model_Abstract::cacheSaveProperty()
	 * @return array(string => string)
	 */
	public function departments() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => string) $result */
			foreach (Df_InTime_Api::s()->представительства() as $department) {
				/** @var array(string => mixed) $department */
				/** @var array(string => mixed) $data */
				$data = $department['AppendField'];
				$result[$department['Code']] = implode(', ', array(
					$this->field($data, 'City'), $this->field($data, 'Adress')
				));
			}
			asort($result);
			/**
			 * 2015-04-05
			 * Для корректной работы валидатора «validate-select»
			 * служебная псевдоопция должна иметь либо пустой код, либо код «none».
			 * @see js/prototype/validation.js
				['validate-select', 'Please select an option.', function(v) {
					return ((v != "none") && (v != null) && (v.length != 0));
				}],
			 */
			$this->{__METHOD__} = array('' => '-- выберите представительство --') + $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @see Df_Core_Model_Abstract::cachedGlobal()
	 * @return string[]
	 */
	protected function cachedGlobal() {return self::m(__CLASS__, 'departments');}

	/**
	 * @used-by departments()
	 * @param array(array(string => string)) $data
	 * @param string $name
	 * @return string
	 */
	private function field(array $data, $name) {
		/** @var string|null $result */
		foreach ($data as $item) {
			/** @var array(string => string) $item */
			if ($name === dfa($item, 'AppendFieldName')) {
				$result = dfa($item, 'AppendFieldValue');
				break;
			}
		}
		return isset($result) ? $result : null;
	}
}