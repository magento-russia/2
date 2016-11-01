<?php
namespace Df\YandexMarket\Config\Backend;
use Df\YandexMarket\Categories as C;
class Category extends \Mage_Eav_Model_Entity_Attribute_Backend_Abstract {
	/**
	 * @overide
	 * @param \Varien_Object $object
	 * @return $this
	 */
	public function beforeSave($object) {
		try {
			/** @var string|null $value */
			$value = $object->getData($this->getAttribute()->getAttributeCode());
			if ($value && !in_array($value, C::paths())) {
				df_error_html(df_url_bake(
					"Категория «{$value}» отсутствует в [[официальном перечне Яндекс.Маркета]]."
					,df_cfgr()->yandexMarket()->other()->getCategoriesReferenceBookUrl()
				));
			}
		}
		catch (\Exception $e) {
			df_exception_to_session($e);
		}
		return $this;
	}
}