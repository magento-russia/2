<?php
/**
 * @param string $resourceId
 * @return bool
 */
function df_admin_allowed($resourceId) {return df_admin_session()->isAllowed($resourceId);}

/** @return void */
function df_admin_begin() {Df_Admin_Model_Mode::s()->begin();}

/**
 * @used-by Df_Cms_Block_Admin_Hierarchy_Edit_Form::getPageGridButtonsHtml()
 * @used-by Df_Cms_Block_Admin_Hierarchy_Edit_Form::getTreeButtonsHtml()
 * @used-by Df_Cms_Block_Admin_Hierarchy_Edit_Form::getPagePropertiesButtons()
 * @used-by Df_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Balance::getDeleteOrphanBalancesButton()
 * @used-by Df_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Balance_Grid::_afterToHtml()
 * @param array(string => mixed) $params
 * @return string
 */
function df_admin_button(array $params) {
	return df_render('Mage_Adminhtml_Block_Widget_Button', $params);
}

/**
 * 2015-04-01
 * @used-by Df_Cms_Block_Admin_Page_Version_Edit::__construct()
 * @used-by Df_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Balance::getDeleteOrphanBalancesButton()
 * @used-by Df_Invitation_Block_Adminhtml_Invitation_View::_prepareLayout()
 * @used-by Df_Logging_Block_Details::_construct()
 * @used-by Df_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Balance_Grid::_afterToHtml()
 * @param string $url
 * @return string
 */
function df_admin_button_location($url) {
	if (df_contains($url, '*')) {
		$url = df_url_admin($url);
	}
	$url = df_ejs($url);
	return "setLocation('{$url}');";
}

/**
 * @param object $object
 * @param string $method
 * @param array(string => mixed) $parameters [optional]
 * @return void
 * @throws Exception
 */
function df_admin_call($object, $method, array $parameters = array()) {
	Df_Admin_Model_Mode::s()->call($object, $method, $parameters);
}

/** @return void */
function df_admin_end() {Df_Admin_Model_Mode::s()->end();}

/**
 * 2015-03-08
 * Алгоритм аналогичен методам
 * @see Df_Catalog_Model_Product::getId()
 * @see Df_Catalog_Model_Category::getId()
 * Если идентификатор объекта не равен null, то приводим его к типу int.
 * Вдруг он является строкой (представляющей число), например, после запроса из БД?
 * Вот поэтому и приводим к int.
 * Вместо (int) ещё надёжнее использовать @see df_nat0(),
 * но мы намеренно не используем в данном случае @see df_nat0() ради ускорения работы функции
 * (мы не ожидаем, что кто-то специально запихнёт в объект данного класса
 * строку, не представляющую число).
 * @param bool $required [optional]
 * @return int
 */
function df_admin_id($required = true) {
	/** @var Mage_Admin_Model_User|null $user */
	$user = df_admin_user($required);
	/** @var int|string|null $result */
	$result = $user->getId();
	if (is_null($result) && $required) {
		df_admin_user_error();
	}
	return is_null($result) ? null : (int)$result;
}

/**
 * @param bool $required [optional]
 * @return string|null
 */
function df_admin_name($required = true) {
	/** @var Mage_Admin_Model_User|null $user */
	$user = df_admin_user($required);
	return $user ? $user->getUsername() : null;
}

/** @return Mage_Admin_Model_Session */
function df_admin_session() {return Mage::getSingleton('admin/session');}

/**
 * @param bool $required [optional]
 * @return Mage_Admin_Model_User|null $user
 */
function df_admin_user($required = true) {
	/** @var Mage_Admin_Model_User|null $user $result */
	$result = df_admin_session()->getData('user');
	if (!$result && $required) {
		df_admin_user_error();
	}
	return $result;
}

/**
 * @used-by df_admin_id()
 * @used-by df_admin_user()
 * @return void
 * @throws Exception
 */
function df_admin_user_error() {
	df_error(
		'Программа пытается узнать данные администратора, однако администратор ещё не авторизован.'
		. "\nЭто ошибка программиста."
	);
}
