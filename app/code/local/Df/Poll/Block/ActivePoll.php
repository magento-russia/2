<?php
class Df_Poll_Block_ActivePoll extends Mage_Poll_Block_ActivePoll {
	/**
	 * @override
	 * @see Mage_Poll_Block_ActivePoll::getCacheKeyInfo()
	 * @used-by Df_Core_Block_Abstract::getCacheKey()
	 * Обратите внимание, что родительский метод
	 * @see Mage_Poll_Block_ActivePoll::getCacheKeyInfo() вызывать неправильно.
	 * Родительский метод вызывает метод своего родителя
	 * @see Mage_Core_Block_Template::getCacheKeyInfo(), а тот, в свою очередь,
	 * возвращает РАЗНЫЕ данные при загрузке кэша и при сохранении кэша,
	 * и, таким образом, кэш не работает: данные записываются в кэш по одному ключу,
	 * а загружаются совсем по другому.
	 *
	 * Обратите внимание, что кэш блока @see Mage_Poll_Block_ActivePoll
	 * не работает и в Magento Community Edition:
	 * таким образом, это дефект Magento Community Edition.
	 *
	 * Ниже — конкретные данные из магазина rukodeling.ru.
	 * При загрузке кэша метод @see Mage_Core_Block_Template::getCacheKeyInfo()
	 * формирует ключ на основе следующих данных:
		Array
		(
			[0] => BLOCK_TPL
			[1] => default
			[2] => frontend\base\theme177\template\
			[template] =>
		)
	 * При сохранении кэша метод @see Mage_Core_Block_Template::getCacheKeyInfo()
	 * формирует ключ на основе следующих данных:
		Array
		(
			[0] => BLOCK_TPL
			[1] => default
			[2] => frontend\base\default\template\poll/active.phtml
			[template] => poll/active.phtml
		)
	 * @return string[]
	 */
	public function getCacheKeyInfo() {
		return array(
			get_class($this)
   			, df_store()->getCode()
			,serialize($this->_templates)
			,implode('-', $this->getVotedPollsIds())
			, (int)Mage::getSingleton('core/session')->getJustVotedPoll()
		);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		/**
		 * Чтобы блок кэшировался стандартным, заложенным в @see Mage_Core_Block_Abstract способом,
		 * продолжительность хранения кэша надо указывать обязательно,
		 * потому что значением продолжительности по умолчанию является «null»,
		 * что в контексте @see Mage_Core_Block_Abstract
		 * (и в полную противоположность Zend Framework
		 * и всем остальным частям Magento, где используется кэширование)
		 * означает, что блок не удет кэшироваться вовсе!
		 * @used-by Mage_Core_Block_Abstract::_loadCache()
		 */
		$this->setData('cache_lifetime', Df_Core_Block_Template::CACHE_LIFETIME_STANDARD);
	}
}