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
			$this
				->addItemsForTheCurrentPageApplicators()
				->mergeItemsWithTheSamePosition()
				->_setIsLoaded(true)
			;
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
		$verticalOrderings = array();
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
	 * @return string
	 */
	protected function getItemClass() {return Df_Cms_Model_ContentsMenu::_CLASS;}

	/** @return Df_Cms_Model_ContentsMenu_Collection */
	private function addItemsForTheCurrentPageApplicators() {
		foreach (Df_Cms_Model_Registry::s()->getApplicators() as $applicator) {
			/** @var Df_Cms_Model_ContentsMenu_Applicator $applicator */
			// Должна ли данная рубрика отображаться в каком-либо меню на текущей странице?
			if ($applicator->isApplicableToTheCurrentPage()) {
				/**
				 * Итак, рубрика должна отображаться в каком-то меню на текущей странице:
				 * либо в одном из уже присутствующик в коллекции меню, либо в новом меню.
				 * Нам проще для каждой рубрики добавлять новое меню,
				 * и лишь потом объединить несколько меню в одно.
				 */
				/** @var Df_Cms_Model_ContentsMenu $menu */
				$menu =
					Df_Cms_Model_ContentsMenu::i(
						array(
							Df_Cms_Model_ContentsMenu::P__POSITION => $applicator->getPosition()
							,Df_Cms_Model_ContentsMenu
								::P__VERTICAL_ORDERING => $applicator->getVerticalOrdering()
						)
					)
				;
				$menu->getApplicators()->addItem($applicator);
				$this->addItem($menu);
				$this->getPosition($applicator->getPosition())->addItem($menu);
			}
		}
		return $this;
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
	 * @return Df_Cms_Model_ContentsMenu_Collection
	 */
	private function mergeItemsWithTheSamePosition() {
		foreach ($this->getPositions() as $position) {
			/** @var Df_Cms_Model_ContentsMenu_Collection $position */
			$position->mergeItems();
		}
		return $this;
	}

	const _CLASS = __CLASS__;
	const P__ID = 'id';
	/** @return Df_Cms_Model_ContentsMenu_Collection */
	public static function i() {return new self;}
}