<?php
/**
 * @param string $resourceId
 * @return bool
 */
function rm_admin_allowed($resourceId) {return rm_admin_session()->isAllowed($resourceId);}

/**
 * @used-by Df_Cms_Block_Admin_Hierarchy_Edit_Form::getPageGridButtonsHtml()
 * @used-by Df_Cms_Block_Admin_Hierarchy_Edit_Form::getTreeButtonsHtml()
 * @used-by Df_Cms_Block_Admin_Hierarchy_Edit_Form::getPagePropertiesButtons()
 * @used-by Df_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Balance::getDeleteOrphanBalancesButton()
 * @used-by Df_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Balance_Grid::_afterToHtml()
 * @param array(string => mixed) $params
 * @return string
 */
function rm_admin_button(array $params) {
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
function rm_admin_button_location($url) {
	if (df_contains($url, '*')) {
		$url = rm_url_admin($url);
	}
	$url = df_ejs($url);
	return "setLocation('{$url}');";
}

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
function rm_admin_id($required = true) {
	/** @var Mage_Admin_Model_User|null $user */
	$user = rm_admin_user($required);
	/** @var int|string|null $result */
	$result = $user->getId();
	if (is_null($result) && $required) {
		rm_admin_user_error();
	}
	return is_null($result) ? null : (int)$result;
}

/**
 * @param bool $required [optional]
 * @return string|null
 */
function rm_admin_name($required = true) {
	/** @var Mage_Admin_Model_User|null $user */
	$user = rm_admin_user($required);
	return $user ? $user->getUsername() : null;
}

/** @return Mage_Admin_Model_Session */
function rm_admin_session() {return Mage::getSingleton('admin/session');}

/**
 * @param bool $required [optional]
 * @return Mage_Admin_Model_User|null $user
 */
function rm_admin_user($required = true) {
	/** @var Mage_Admin_Model_User|null $user $result */
	$result = rm_admin_session()->getData('user');
	if (!$result && $required) {
		rm_admin_user_error();
	}
	return $result;
}

/**
 * @used-by rm_admin_id()
 * @used-by rm_admin_user()
 * @return void
 * @throws Exception
 */
function rm_admin_user_error() {
	df_error(
		'Программа пытается узнать данные администратора, однако администратор ещё не авторизован.'
		. "\nЭто ошибка программиста."
	);
}
