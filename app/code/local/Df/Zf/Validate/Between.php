<?php
class Df_Zf_Validate_Between extends Zend_Validate_Between {
	/**
	 * Sets validator options
	 * Accepts the following option keys:
	 *   'min' => scalar, minimum border
	 *   'max' => scalar, maximum border
	 *   'inclusive' => boolean, inclusive border values
	 * @override
	 * @param array|Zend_Config $options
	 */
	public function __construct(array $options) {
		/**
		 * Спецификация конструктора класса Zend_Validate_Between
		 * различается между Zend Framework 1.9.6 (Magento 1.4.0.1)
		 * и Zend Framework 1.11.1 (Magento 1.5.0.1).
		 *
		 * Именно для устранения для пользователя данных различий
		 * служит наш класс-посредник Df_Zf_Validate_Between
		 */
		if (version_compare (Zend_Version::VERSION, '1.10', '>=')) {
			/** @noinspection PhpParamsInspection */
			parent::__construct($options);
		}
		else {
			/** @noinspection PhpParamsInspection */
			parent::__construct(
				df_a($options, 'min')
				,df_a($options, 'max')
				,df_a($options, 'inclusive')
			);
		}
	}
}