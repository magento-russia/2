<?php
namespace Df\Framework\Form;
use \Varien_Data_Form_Element_Abstract as AE;
/**
 * 2015-11-22
 * Пока что это класс используется только ради описания магических методов в шапке.
 * @method string|null getClass()
 *
 * 2016-05-30
 * @method string|null getComment()
 * https://github.com/magento/magento2/blob/a5fa3af3/app/code/Magento/Config/Block/System/Config/Form/Field.php#L82-L84
	if ((string)$element->getComment()) {
		$html .= '<p class="note"><span>' . $element->getComment() . '</span></p>';
	}
 *
 * @method string|null getContainerClass()
 * @method string|null getCssClass()
 * @method string|null getExtType()
 * @method mixed[] getFieldConfig()
 * @method string|null getFieldExtraAttributes()
 * @method string|null getLabel()
 * @method string|null getLabelPosition()
 * @method string|null getNote()
 * @method bool|null getNoDisplay()
 * @method bool|null getNoWrapAsAddon()
 * @method bool|null getRequired()
 * @method string|null getScopeLabel()
 * @method string|null getTitle()
 * @method $this setAfterElementHtml(string $value)
 * @method $this setContainerClass(string $value)
 * @method $this setLabelPosition(string $value)
 * @method $this setNote(string $value)
 */
class Element extends AE {}