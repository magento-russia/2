<?php
/**
 * В качестве параметра $block можно передавать:
 * 1) объект-блок
 * 2) класс блока в стандартном формате
 * 3) класс блока в формате Magento
 * 4) пустое значение: в таком случае будет создан блок типа @see Mage_Core_Block_Template
 * @used-by rm_block_l()
 * @used-by rm_render()
 * @param string|Mage_Core_Block_Abstract|null $block [optional]
 * @param string|array(string => mixed) $params [optional]
 * @return Mage_Core_Block_Abstract
 * @throws Exception
 */
function rm_block($block = null, $params = array()) {
	/** @var Mage_Core_Block_Abstract $result */
	if (is_string($params)) {
		$params = array('template' => $params);
	}
	df_param_array($params, 2);
	if (!$block) {
		$block = 'Mage_Core_Block_Template';
	}
	if (!is_object($block)) {
		df_param_string_not_empty($block, 0);
		$result = rm_layout()->getBlockInstance($block, $params);
	}
	else {
		/**
		 * @uses Mage_Core_Model_Layout::createBlock() не добавит параметры к блоку,
		 * если в этот метод передать не тип блока, а еще созданный объект-блок.
		 */
		df_assert($block instanceof Mage_Core_Block_Abstract);
		$block->addData($params);
		$result = $block;
	}
	return $result;
}

/**
 * Блок «head» может отсутствовать на некоторых страницах (например, при импорте товаров).
 * В случае отсутствия блока «head» создаём объект-заглушку.
 * Этот приём называется «null object»: http://en.wikipedia.org/wiki/Null_Object_pattern
 * В данном случае этот приём очень удобен, посколько позволяет избежать
 * бесчисленных проверок на существование объекта «head» перед обращением к нему.
 * @used-by Df_Pd4_IndexController::indexAction()
 * @used-by rm_page_global_css()
 * @used-by rm_page_global_js()
 * @used-by rm_page_skin_css()
 * @used-by rm_page_skin_js()
 * @return Mage_Page_Block_Html_Head
 */
function rm_block_head() {
	/** @var Mage_Page_Block_Html_Head $result */
	$result = rm_layout()->getBlock('head');
	if (!$result) {
		/** @var Mage_Page_Block_Html_Head $nullObject */
		static $nullObject; if (!$nullObject) {$nullObject = new Mage_Page_Block_Html_Head;}
		$result = $nullObject;
	}
	return $result;
}

/**
 * 2015-03-30
 * Создаёт блок и добавляет его в макет.
 * Используйте эту функцию, если блок нужно нарисовать не сразу
 * (посредством явного и немедленного вызова @see Mage_Core_Block_Abstract::toHtml()),
 * а при отображении макета.
 *
 * Если блок нуждается в методе @see Mage_Core_Block_Abstract::getLayout(),
 * но блок нужно нарисовать сразу, но используйте функцию @see rm_render_l():
 * она позволяет блоку использовать макет, но не добавляет блок в макет,
 * а рисует блок сразу.
 *
 * Если блок не нуждается в макете, то используйте функции
 * @see rm_block(), @see rm_render(), @see rm_render_simple()
 *
 * В качестве параметра $block можно передавать:
 * 1) объект-блок
 * 2) класс блока в стандартном формате
 * 3) класс блока в формате Magento
 * 4) пустое значение: в таком случае будет создан блок типа @see Mage_Core_Block_Template
 *
 * @used-by Df_Banner_Block_Adminhtml_Banner_Grid::i()
 * @used-by Df_Banner_Block_Adminhtml_Banneritem_Grid::i()
 * @used-by Df_Banner_Adminhtml_BannerController::editAction()
 * @used-by Df_Banner_Adminhtml_BanneritemController::editAction()
 * @used-by Df_Cms_Block_Frontend_Menu_Contents::i()
 * @used-by Df_Logging_Block_Index_Grid::i()
 * @used-by Df_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward::_prepareLayout()
 * @used-by Df_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_History::i()
 * @used-by Df_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_History_Grid::i()
 * @used-by Df_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management::_prepareLayout()
 * @used-by Df_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Balance::_prepareLayout()
 *
 * @param string|Mage_Core_Block_Abstract|null $block [optional]
 * @param string|array(string => mixed) $params [optional]
 * @param bool $addToLayout [optional]
 * @return Mage_Core_Block_Abstract
 * @throws Exception
 */
function rm_block_l($block, $params = array()) {
	/** @var Mage_Core_Block_Abstract $result */
	$result = rm_layout()->createBlock(rm_block($block, $params));
	if (!$result) {
		df_error("Не могу создать блок класса «{$block}».\nСмотрите отчёт в папке var/log.");
	}
	return $result;
}

/**
 * @used-by Df_Tweaks_Observer::controller_action_layout_generate_blocks_after()
 * @used-by Df_Tweaks_Model_Handler_AdjustBanners::handle()
 * @used-by Df_Tweaks_Model_Handler_AdjustCartPage::handle()
 * @used-by Df_Tweaks_Model_Handler_AdjustNewsletterSubscription::handle()
 * @used-by Df_Tweaks_Model_Handler_AdjustPaypalLogo::handle()
 * @used-by Df_Tweaks_Model_Handler_AdjustPoll::handle()
 * @used-by Df_Tweaks_Model_Handler_Remover::handle()
 * @used-by Df_Tweaks_Model_Handler_ProductBlock_Wishlist::handle()
 * @used-by Df_Tweaks_Model_Handler_ProductBlock_Recent_Viewed::handle()
 * @param Mage_Core_Block_Abstract|string|string[] $block
 * @return void
 */
function rm_block_remove($block) {
	if (func_num_args() > 1) {
		$block = func_get_args();
	}
	if (is_array($block)) {
		array_map(__FUNCTION__, $block);
	}
	else {
		/**
		 * Что интересно, мы можем эффективно (быстро) удалять блоки и по их типу.
		 *
		 * Layout, при создании блока, устанавливает блоку его тип в формате Magento:
		 * $block->setType($type);
		 *
		 * Поэтому, по событию controller_action_layout_generate_blocks_after
		 * мы можем сформировать единократно карту тип блока => блок,
		 * и затем быстро находить блоки по их типу.
		 */
		if (is_string($block)) {
			$block = rm_layout()->getBlock($block);
		}
		if ($block instanceof Mage_Core_Block_Abstract) {
			rm_layout()->unsetBlock($block->getNameInLayout());
			/** @var Mage_Core_Block_Abstract|null $parent */
			$parent = $block->getParentBlock();
			/**
			 * Как ни странно — родительского блока может не быть.
			 * Такое происходит, когда в файлах layout уже вызвали unsetChild.
			 * Непонятно, почему после unset блок всё-таки доходит сюда.
			 */
			if ($parent) {
				$parent->unsetChild($block->getBlockAlias());
				/**
				 * Заплатка для Magento CE 1.6.2.0 и более ранних версий
				 * Метод unsetChild в этих версиях дефектен
				 */
				/** @var bool $le_1_6_0_2 */
				static $le_1_6_0_2;
				if (is_null($le_1_6_0_2)) {
					$le_1_6_0_2 = df_magento_version('1.6.2.0', '<=');
				}
				if ($le_1_6_0_2) {
					$parent->unsetChild($block->getNameInLayout());
				}
				/**
				 * После unsetChild надо обязательно вызывать sortChildren,
				 * иначе блоки дублируются: http://magento-forum.ru/topic/1491/
				 *
				 * Дублирование происходит, потому что unsetChild приводит к вызову PHP unset,
				 * а unset не обновляет натуральные индексы массива:
				 * 'It should be noted that unset() will keep indexes untouched,
				 * which is what you'd expect when using string indexes (array as hashtable),
				 * but can be quite surprising when dealing with integer indexed arrays:'
				 *
				 * Это приводить потом к неправильному поведению sortChildren
				 * (и array_splice внутри него)
				 * при отображении страницы
				 */
				/**
				 * НАЧАЛО НОВОГО АЛГОРИТМА
				 * 2014-12-08
				 * Оказалось, что прежний алгоритм не всегда решает проблему задваивания блока
				 * при использовании некоторых тем:
				 * http://magento-forum.ru/topic/2617/
				 * http://magento-forum.ru/topic/3374/
				 * http://magento-forum.ru/topic/3586/
				 * http://magento-forum.ru/topic/3861/
				 * http://magento-forum.ru/topic/4142/
				 * Поэтому добавил к прежнему алгоритму дополнение.
				 * Теперь вроде всё правильно работает.
				 */
				/** @var array(string => Mage_Core_Block_Abstract) $orderedMapFromNamesToBlocks */
				$orderedMapFromNamesToBlocks = $parent->getSortedChildBlocks();
				$parent->unsetChildren();
				foreach ($orderedMapFromNamesToBlocks as $childName => $child) {
					/** @var string $childName */
					/** @var Mage_Core_Block_Abstract $child */
					/** @var string $alias */
					/**
					 * 2015-03-18
					 * В очень коряво разработанном магазине autosp.kz
					 * в качестве $child попало значение типа «boolean».
					 */
					if (is_object($child)) {
						$alias = $child->getBlockAlias();
						if (!$alias) {
							$alias = $childName;
						}
						$parent->append($child, $alias);
					}
				}
				// КОНЕЦ НОВОГО АЛГОРИТМА
				/**
				 * На всякий случай оставляем и вызов @uses Mage_Core_Block_Abstract::sortChildren().
				 * К сожалению, нельзя здесь для проверки публичности метода
				 * использовать @see is_callable(),
				 * потому что наличие @see Varien_Object::__call()
				 * приводит к тому, что @see is_callable() всегда возвращает true.
				 * В Magento 1.4 - 1.5
				 * метод @uses Mage_Core_Block_Abstract::sortChildren() отсутствует
				 */
				if (method_exists($parent, 'sortChildren')) {
					call_user_func(array($parent, 'sortChildren'));
				}
			}
		}
	}
}

/**
 * @param string $handle
 * @return bool
 */
function rm_handle_presents($handle) {
	/** @uses array_flip() / @uses isset() работает быстрее, чем @see in_array() */
	/** @var bool[] $cache */
	static $handles;
	if (is_null($handles)) {
		$handles = array_flip(rm_handles());
	}
	return isset($handles[$handle]);
}

/** @return string[] */
function rm_handles() {return rm_layout()->getUpdate()->getHandles();}

/** @return Df_Core_Model_Layout */
function rm_layout() {return Mage::getSingleton('core/layout');}

/**
 * 2015-03-30
 * Эта функция никем не используется.
 * @param string|string[] $file
 * @return void
 */
function rm_page_global_css($file) {
	if (func_num_args() > 1) {
		$file = func_get_args();
	}
	is_array($file) ? array_map(__FUNCTION__, $file) : rm_block_head()->addItem('js_css', $file);
}

/**
 * 2015-03-30
 * Эта функция никем не используется.
 * @param string|string[] $file
 * @return void
 */
function rm_page_global_js($file) {
	if (func_num_args() > 1) {
		$file = func_get_args();
	}
	is_array($file) ? array_map(__FUNCTION__, $file) : rm_block_head()->addItem('js', $file);
}

/**
 * 2015-03-30
 * Эта функция никем не используется.
 * @param string|string[] $file
 * @return void
 */
function rm_page_skin_css($file) {
	if (func_num_args() > 1) {
		$file = func_get_args();
	}
	is_array($file) ? array_map(__FUNCTION__, $file) : rm_block_head()->addItem('skin_css', $file);
}

/**
 * 2015-03-30
 * Эта функция никем не используется.
 * @param string|string[] $file
 * @return void
 */
function rm_page_skin_js($file) {
	if (func_num_args() > 1) {
		$file = func_get_args();
	}
	is_array($file) ? array_map(__FUNCTION__, $file) : rm_block_head()->addItem('skin_js', $file);
}

/**
 * 2015-03-30
 * Обратите внимание, что эта функция:
 * 1) не добавляет блок в макет, в отличие от @see rm_block_l()
 * 2) отображает блок упрощённым методом @uses Mage_Core_Block_Abstract::toHtmlFast()
 * вместо @see Mage_Core_Block_Abstract::toHtml()
 *
 * В качестве параметра $block можно передавать:
 * 1) объект-блок
 * 2) класс блока в стандартном формате
 * 3) класс блока в формате Magento
 * 4) пустое значение: в таком случае будет создан блок типа @see Mage_Core_Block_Template
 * @used-by rm_admin_button()
 * @used-by rm_render_l()
 * @used-by rm_render_simple()
 * @used-by Df_Checkout_Block_Frontend_Ergonomic_Address::r()
 * @used-by Df_Checkout_Block_Frontend_Ergonomic_Address_HtmlSelect::_toHtml()
 * @used-by Df_Chronopay_Block_Gate_Request::r()
 * @used-by Df_Localization_Block_Admin_Verification_File::r()
 * @used-by Df_Pd4_Block_Document::getRowsHtml()
 * @used-by Df_Pd4_Block_LinkToDocument_ForAnyOrder::r()
 * @used-by Df_PromoGift_Block_Chooser_Gift::r()
 * @used-by Df_PromoGift_Block_Chooser_Product::r()
 * @used-by Df_PromoGift_Block_Chooser_PromoAction::r()
 * @used-by Df_Sales_Block_Order_Email_Comments::r()
 * @param string|Mage_Core_Block_Abstract|null $block [optional]
 * @param string|array(string => mixed) $params [optional]
 * @return string
 * @throws Exception
 */
function rm_render($block, $params = array()) {return rm_block($block, $params)->toHtmlFast();}

/**
 * 2015-04-01
 * @param Mage_Core_Block_Abstract $parent
 * @param string|Mage_Core_Block_Abstract|null $block [optional]
 * @param string|array(string => mixed) $params [optional]
 * @return string
 * @throws Exception
 */
function rm_render_child(Mage_Core_Block_Abstract $parent, $block, $params = array()) {
	return rm_block($block, $params)->setParentBlock($parent)->toHtmlFast();
}

/**
 * 2015-04-01
 * Используйте эту функцию для отображения блоков, которые используют метод
 * @see Mage_Core_Block_Abstract::getLayout()
 * Если блок не нужно рисовать сразу, а нужно непременно добавить в макет,
 * то используйте функцию @see rm_block_l()
 *
 * @used-by Df_Cms_Block_Admin_Hierarchy_Widget_Chooser::prepareElementHtml()
 * @param string|Mage_Core_Block_Abstract|null $block [optional]
 * @param string|array(string => mixed) $params [optional]
 * @return string
 * @throws Exception
 */
function rm_render_l($block, $params = array()) {
	return rm_render(rm_block($block, $params)->setLayout(rm_layout()));
}

/**
 * 2015-03-30
 * Обратите внимание, что эта функция:
 * 1) не добавляет блок в макет, в отличие от @see rm_block_l()
 * 2) отображает блок упрощённым методом @uses Mage_Core_Block_Abstract::toHtmlFast()
 * вместо @see Mage_Core_Block_Abstract::toHtml()
 *
 * @used-by rm_render_simple_child()
 * @used-by Df_Qa_Message::message()
 * @param string $template
 * @param string|array(string => mixed) $params [optional]
 * @return string
 */
function rm_render_simple($template, array $params = array()) {
	return rm_render(null, array('template' => $template) + $params);
}

/**
 * 2015-04-01
 * @param Mage_Core_Block_Template $parent
 * @param string $templateShort
 * @param string|array(string => mixed) $params [optional]
 * @return string
 */
function rm_render_simple_child(Mage_Core_Block_Template $parent, $templateShort, array $params = array()) {
	return rm_render_simple(Mage::getDesign()->getTemplateFilename(
		/**
		 * Обратите внимание, что мы намеренно используем @uses Mage_Core_Block_Template::getTemplate()
		 * вместо @see Mage_Core_Block_Template::getTemplateFile(),
		 * чтобы позволить домернему шаблону быть перекрытым (например, в папке priority или base)
		 * независимо от шаблона родителя и наоборот.
		 * @var string $template
		 */
		rm_strip_ext($parent->getTemplate()) . "/{$templateShort}.phtml"
		/** по аналогии с @see Mage_Core_Block_Template::getTemplateFile() */
		, df_clean(array('_relative' => true, '_area' => $parent->getArea()))
	), $params);
}

