<?php
class Df_Cms_Model_ContentsMenu_Collection extends Df_Varien_Data_Collection {
	/**
	 * Идентификатор нам нужен для формирования коллекции коллекций
	 * (а коллекции коллекций нужны для группировки коллекций меню 
	 * по местам их размещения на экране)
	 * @return string
	 */
	public function getId() {return $this->getFlag(self::P__ID);}

	/**
	 * Коллекции всех меню для конкретных мест на экране
	 * @return Df_Cms_Model_ContentsMenu_Collection
	 */
	public function getPositions() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = self::i();
		}
		return $this->{__METHOD__};
	}

	/**
	 * Обратите внимание, что для загрузки всех меню текущей страницы
	 * мы не переопределяем метод Varien_Data_Collection::loadData(),
	 * потому что у нас будут существовать и другие коллекции меню,
	 * которые будут содержать не все меню текущей страницы, а набор меню
	 * по другим критериям: в частности, все меню для конкретного места текущей страницы.
	 * @return Df_Cms_Model_ContentsMenu_Collection
	 */
	public function loadItemsForTheCurrentPage() {
		if (!$this->isLoaded()) {
			$this->addItemsForTheCurrentPageApplicators();
			$this->mergeItemsWithTheSamePosition();
			$this->_setIsLoaded(true);
		}
		return $this;
	}

	/**
	 * Этот метод предназначен не для коллекции всех меню,
	 * а для коллекции меню текущего места на экране.
	 * Метод объединяет меню, которые на экране расположены рядом.
	 * @return Df_Cms_Model_ContentsMenu_Collection
	 */
	public function mergeItems() {
		/** @var Df_Cms_Model_ContentsMenu[] $verticalOrderings */
		$verticalOrderings = [];
		foreach ($this->getItems() as $item) {
			/** @var Df_Cms_Model_ContentsMenu $item */
			/** @var bool $merged */
			$merged = false;
			foreach ($verticalOrderings as $currentVerticalOrdering => $currentMenu) {
				/** @var int $currentVerticalOrdering */
				/** @var Df_Cms_Model_ContentsMenu $currentMenu */
				if (2 > abs($item->getVerticalOrdering() - $currentVerticalOrdering)) {
					$currentMenu->merge($item);
					$this->removeItemByKey($item->getId());
					$merged = true;
					break;
				}
			}
			if (!$merged) {
				$verticalOrderings[$item->getVerticalOrdering()] = $item;
			}
		}
		return $this;
	}

	/**
	 * @override
	 * @see Df_Varien_Data_Collection::itemClass()
	 * @used-by Df_Varien_Data_Collection::addItem()
	 * @return string
	 */
	protected function itemClass() {return Df_Cms_Model_ContentsMenu::class;}

	/** @return void */
	private function addItemsForTheCurrentPageApplicators() {
		foreach (Df_Cms_Model_Registry::s()->getApplicators() as $applicator) {
			/** @var Df_Cms_Model_ContentsMenu_Applicator $applicator */
			// Должна ли данная рубрика отображаться в каком-либо меню на текущей странице?
			if ($applicator->isApplicableToTheCurrentPage()) {
				// Итак, рубрика должна отображаться в каком-то меню на текущей странице:
				// либо в одном из уже присутствующик в коллекции меню, либо в новом меню.
				// Нам проще для каждой рубрики добавлять новое меню,
				// и лишь потом объединить несколько меню в одно.
				/** @var Df_Cms_Model_ContentsMenu $menu */
				$menu = Df_Cms_Model_ContentsMenu::i($applicator);
				$menu->getApplicators()->addItem($applicator);
				$this->addItem($menu);
				$this->getPosition($applicator->getPosition())->addItem($menu);
			}
		}
	}

	/**
	 * @param string $position
	 * @return Df_Cms_Model_ContentsMenu_Collection
	 */
	private function getPosition($position) {
		df_param_string($position, 0);
		/** @var Df_Cms_Model_ContentsMenu_Collection $result */
		$result = $this->getPositions()->getItemById($position);
		if (is_null($result)) {
			$result = Df_Cms_Model_ContentsMenu_Collection::i();
			$result->setFlag(Df_Cms_Model_ContentsMenu_Collection::P__ID, $position);
			$this->getPositions()->addItemNotVarienObject($result);
		}
		df_assert($result instanceof Df_Cms_Model_ContentsMenu_Collection);
		return $result;
	}

	/**
	 * Ранее мы создали отдельное меню для каждой из корневых рубрик,
	 * которые должны отображаться в меню на текущей странице.
	 * Теперь надо объединить те меню,
	 * которые на экране будут расположены на одном и том же месте.
	 * @uses Df_Cms_Model_ContentsMenu_Collection::mergeItems()
	 * @return void
	 */
	private function mergeItemsWithTheSamePosition() {$this->getPositions()->walk('mergeItems');}


	const P__ID = 'id';
	/** @return Df_Cms_Model_ContentsMenu_Collection */
	public static function i() {return new self;}
}