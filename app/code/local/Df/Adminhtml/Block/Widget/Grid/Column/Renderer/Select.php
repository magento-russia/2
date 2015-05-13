<?php
/**
 * @method Varien_Object getColumn()
 */
class Df_Adminhtml_Block_Widget_Grid_Column_Renderer_Select
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	/**
	 * @override
	 * @param Varien_Object $row
	 * @return string
	 */
	public function render(Varien_Object $row) {
		/** @var Df_Core_Model_Format_Html_Select $select */
		$select =
			Df_Core_Model_Format_Html_Select::i(
				array(
					Df_Core_Model_Format_Html_Select::P__ATTRIBUTES => $this->getAttributes()
					,Df_Core_Model_Format_Html_Select::P__OPTIONS => $this->getOptions()
					,Df_Core_Model_Format_Html_Select::P__SELECTED => $this->getValueSelected($row)
				)
			)
		;
		/** @var string $result */
		$result = $select->render();
		df_result_string($result);
		return $result;
	}

	/** @return mixed[] */
	private function getAttributes() {
		/** @var mixed[] $attributes */
		$result = $this->getColumn()->getData('attributes');
		if (is_null($result)) {
			$result = array();
		}
		df_assert_array($result);
		$result['name'] = $this->getName();
		return $result;
	}

	/** @return string */
	private function getName() {
		/** @var string $result */
		$result = $this->getColumn()->getDataUsingMethod('name');
		if (is_null($result)) {
			$result = $this->getColumn()->getDataUsingMethod('id');
		}
		if (is_null($result)) {
			$result = '';
		}
		df_result_string($result);
		return $result;
	}

	/** @return array */
	private function getOptions() {
		/** @var array $result */
		$result = $this->getColumn()->getDataUsingMethod('options');
		df_result_array($result);
		return $result;
	}

	/**
	 * @param Varien_Object $row
	 * @return string|null
	 */
	private function getValueSelected(Varien_Object $row) {
		/** @var string $index */
		$index = $this->getColumn()->getData('index');
		df_assert_string($index);
		/** @var string|null $result */
		$result = $row->getData($index);
		if (!is_null($result)) {
			df_result_string($result);
		}
		return $result;
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @return Df_Adminhtml_Block_Widget_Grid_Column_Renderer_Select
	 */
	public static function i() {return df_block(__CLASS__);}
}