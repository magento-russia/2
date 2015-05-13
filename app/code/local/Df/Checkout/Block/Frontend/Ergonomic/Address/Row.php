<?php
class Df_Checkout_Block_Frontend_Ergonomic_Address_Row extends Df_Core_Block_Abstract_NoCache {
	/** @return Df_Checkout_Model_Collection_Ergonomic_Address_Field */
	public function getFields() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Checkout_Model_Collection_Ergonomic_Address_Field::i();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function _toHtml() {
		/** @var string $result */
		$result =
			(0 === $this->getFields()->count())
			? ''
			: rm_tag(
				'li'
				, array('class' => $this->getCssClassesAsText())
				, implode(
						"\n"
						,array_map(
							array($this, 'wrapField')
							,$this->getFields()->walk('toHtml')
							,$this->getFields()->walk('getType')
						)
					)
				)
		;
		return $result;
	}

	/** @return string */
	private function getCssClassesAsText() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_output()->getCssClassesAsString(
					array(!$this->hasSingleField() ? 'fields' : 'wide')
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function hasSingleField() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = (1 === $this->getFields()->count());
		}
		return $this->{__METHOD__};
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @link http://php.net/manual/en/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * @link http://3v4l.org/OipEQ
	 *
	 * @param string $fieldAsHtml
	 * @param string $fieldType
	 * @return string
	 */
	private function wrapField($fieldAsHtml, $fieldType) {
		return rm_tag(
			'div'
			, array(
				'class' =>
					df_output()->getCssClassesAsString(array(
						'field', rm_sprintf('df-field-%s', $fieldType)
					))
			)
			,$fieldAsHtml
		);
	}

	const _CLASS = __CLASS__;
	/** @return Df_Checkout_Block_Frontend_Ergonomic_Address_Row */
	public static function i() {return df_block(__CLASS__);}
}