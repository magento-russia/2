<?php
class Df_Core_Validator {
	/**
	 * 2015-04-05
	 * @used-by Df_Checkout_Module_Config_Area::getVar()
	 * @param mixed $value
	 * @param Zend_Validate_Interface $validator
	 * @throws Df_Core_Exception
	 * @return void
	 */
	public static function check($value, Zend_Validate_Interface $validator) {
		if (!self::validate($value, $validator)) {
			df_error(
				new Df_Core_Exception(df_cc_n($validator->getMessages())
				, df_print_params(array(
					'Значение' => rm_debug_type($value)
					,'Проверяющий' => get_class($value)
				))
			));
		}
	}

	/**
	 * 2015-04-05
	 * @used-by Df_Core_Block_Abstract::_prop()
	 * @used-by Df_Core_Block_Abstract::_validate()
	 * @used-by Df_Core_Block_Template::_prop()
	 * @used-by Df_Core_Block_Template::_validate()
	 * @used-by Df_Core_Model::_prop()
	 * @used-by Df_Core_Model::_validate()
	 * @param object $object
	 * @param string $key
	 * @param mixed $value
	 * @param Zend_Validate_Interface $validator
	 * @throws Df_Core_Exception
	 * @return void
	 */
	public static function checkProperty($object, $key, $value, Zend_Validate_Interface $validator) {
		if (!self::validate($value, $validator)) {
			df_error(new Df_Core_Exception_InvalidObjectProperty($object, $key, $value, $validator));
		}
	}

	/**
	 * 2015-04-05
	 * @used-by Df_Checkout_Module_Config_Area::getVar()
	 * @used-by resolveForProperty
	 * @param Zend_Validate_Interface|Zend_Filter_Interface|string $validator
	 * @param bool $skipOnNull [optional]
	 * @return Zend_Validate_Interface|Zend_Filter_Interface
	 * @throws Df_Core_Exception
	 */
	public static function resolve($validator, $skipOnNull = false) {
		/** @var Zend_Validate_Interface|Zend_Filter_Interface $result */
		if (is_object($validator)) {
			$validator->{self::$SKIP_ON_NULL} = $skipOnNull;
			$result = $validator;
		}
		else if (is_string($validator)) {
			$result = self::byName($validator, $skipOnNull);
		}
		else {
			df_error(
				"Валидатор/фильтр имеет недопустимый тип: «%s».", gettype($validator)
			);
		}
		if (!$result instanceof Zend_Validate_Interface && !$result instanceof Zend_Filter_Interface) {
			df_error(
				"Валидатор/фильтр имеет недопустимый класс «%s»,"
				. ' у которого отсутствуют требуемые интерфейсы'
				.' Zend_Validate_Interface и Zend_Filter_Interface.'
				, get_class($result)
			);
		}
		return $result;
	}

	/**
	 * 2015-04-05
	 * @used-by Df_Core_Block_Abstract::_prop()
	 * @used-by Df_Core_Block_Template::_prop()
	 * @used-by Df_Core_Model::_prop()
	 * @param object $object
	 * @param Zend_Validate_Interface|Zend_Filter_Interface|string $validator
	 * @param string $key
	 * @param bool $skipOnNull [optional]
	 * @return Zend_Validate_Interface|Zend_Filter_Interface
	 * @throws Df_Core_Exception
	 */
	public static function resolveForProperty($object, $validator, $key, $skipOnNull = false) {
		/** @var Zend_Validate_Interface|Zend_Filter_Interface $result */
		try {
			$result = self::resolve($validator, $skipOnNull);
		}
		catch (Df_Core_Exception $e) {
			$e->comment(df_print_params(array('Класс' => get_class($object), 'Свойство' => $key)));
			throw $e;
		}
		return $result;
	}

	/**
	 * 2015-04-05
	 * Пока никем извне класса не используется, но будет.
	 * @used-by checkProperty()
	 * @param mixed $value
	 * @param Zend_Validate_Interface $validator
	 * @throws Df_Core_Exception
	 * @return bool
	 */
	public static function validate($value, Zend_Validate_Interface $validator) {
		return
			is_null($value)
			&& isset($validator->{self::$SKIP_ON_NULL})
			&& $validator->{self::$SKIP_ON_NULL}
			|| $validator->isValid($value)
		;
	}

	/**
	 * 2015-04-05
	 * @used-by resolve()
	 * @param string $name
	 * @param bool $skipOnNull [optional]
	 * @return Zend_Validate_Interface|Zend_Filter_Interface
	 */
	private static function byName($name, $skipOnNull = false) {
		/** @var array(bool => array(string => Zend_Validate_Interface)) */
		static $cache;
		if (!isset($cache[$skipOnNull][$name])) {
			/** @var array(string => string) $map */
			static $map; if (!$map) {$map = array(
				RM_F_TRIM => 'Df_Zf_Filter_String_Trim'
				,RM_V_ARRAY => 'Df_Zf_Validate_Array'
				,RM_V_BOOL => 'Df_Zf_Validate_Boolean'
				,RM_V_FLOAT => 'Df_Zf_Validate_Float'
				,RM_V_INT => 'Df_Zf_Validate_Int'
				,RM_V_ISO2 => 'Df_Zf_Validate_String_Iso2'
				,RM_V_NAT => 'Df_Zf_Validate_Nat'
				,RM_V_NAT0 => 'Df_Zf_Validate_Nat0'
				,RM_V_STRING => 'Df_Zf_Validate_String'
				,RM_V_STRING_NE => 'Df_Zf_Validate_String_NotEmpty'
			);}
			/** @var Zend_Validate_Interface|Zend_Filter_Interface $result */
			if (isset($map[$name])) {
				$result = new $map[$name];
			}
			else if (@class_exists($name) || @interface_exists($name)) {
				$result = Df_Zf_Validate_Class::i($name);
			}
			else {
				df_error("Система не смогла распознать валидатор «{$name}».");
			}
			$result->{self::$SKIP_ON_NULL} = $skipOnNull;
			$cache[$skipOnNull][$name] = $result;
		}
		return $cache[$skipOnNull][$name];
	}

	/** @var string */
	private static $SKIP_ON_NULL = 'rm_skip_on_null';
}