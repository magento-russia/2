<?php
/**
 * @method bool|null getCanUseDefaultValue()
 * @method bool|null getCanUseWebsiteValue()
 * @method string|null getComment()
 * @method string|null getDefaultValue()
 * @method string|null getExtType()
 * @method Mage_Core_Model_Config_Element getFieldConfig()
 * @method string|null getFieldsetHtmlClass()
 * @method string|null getHint()
 * @method bool|null getInherit()
 * @method string|null getLabel()
 * @method bool|null getNoDisplay()
 * @method string|null getNote()
 * @method string|null getScope()
 * @method string|null getScopeLabel()
 * @method string|null getToggleCode()
 * @method mixed getValue()
 * @method string|null getValueClass()
 * @method mixed[]|null getValues()
 * @method Df_Varien_Data_Form_Element_Abstract setDisabled(bool $value)
 */
class Df_Varien_Data_Form_Element_Abstract extends Varien_Data_Form_Element_Abstract {
	const P__CAN_USE_WEBSITE_VALUE = 'can_use_website_value';
	const P__CAN_USE_DEFAULT_VALUE = 'can_use_default_value';
	const P__DEFAULT_VALUE = 'default_value';
	const P__DISABLED = 'disabled';
	const P__EXT_TYPE = 'ext_type';
	const P__INHERIT = 'inherit';
	const P__LABEL = 'label';
	const P__NAME = 'name';
	const P__REQUIRED = 'required';
	const P__TITLE = 'title';
	const P__VALUES = 'values';
	const TYPE__DATE = 'date';
	const TYPE__DATETIME = 'datetime';
	const TYPE__HIDDEN = 'hidden';
	const TYPE__SELECT = 'select';
	const TYPE__TEXT = 'text';
	const TYPE__TEXTAREA = 'textarea';
}