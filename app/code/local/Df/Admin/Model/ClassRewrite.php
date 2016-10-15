<?php
class Df_Admin_Model_ClassRewrite extends Df_Core_Model {
	/** @return Df_Admin_Model_ClassInfo_Collection */
	public function getDestinations() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Admin_Model_ClassInfo_Collection::i();
		}
		return $this->{__METHOD__};
	}

	/**
	 * Для коллекций
	 * @override
	 * @return string
	 */
	public function getId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = self::makeId($this->getType(), $this->getOrigin()->getId());
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Admin_Model_ClassInfo */
	public function getOrigin() {return $this->cfg(self::$P__ORIGIN);}

	/** @return string */
	public function getType() {return $this->getOrigin()->getType();}

	/** @return bool */
	//public function isConflict() {return 1 < count($this->getDestinations());}

	/** @return bool */
	public function isConflict() {
		if (!isset($this->{__METHOD__})) {
			/** @var bool $result */
			/** @var int $count */
			$count = $this->getDestinations()->count();
			if ($count < 2) {
				// Данный системный класс перекрывается не более, чем одним классом.
				// Точно не конфликт.
				$result = false;
			}
			else if ($count > 3) {
				// Данный системный класс перекрывается более, чем тремя классами.
				// Точно конфликт.
				$result = true;
			}
			else if (3 === $count) {
				// Данный системный класс перекрывается ровно тремя классами.
				// Смотрим родственные отношения этих классов.
				// Алгоритм аналогичен алгоритму для 2-х классов,
				// смотрите комментарии ниже для случая 2-х классов.
				/** @var Df_Admin_Model_ClassInfo[] $items */
				$items = $this->getDestinations()->getItems();
				/** @var Df_Admin_Model_ClassInfo $classInfoActive */
				/** @var Df_Admin_Model_ClassInfo[] $classInfoInactive */
				foreach ($items as $item) {
					/** Df_Admin_Model_ClassInfo $item */
					if ($this->isDestinationActive($item)) {
						$classInfoActive = $item;
					}
					else {
						$classInfoInactive[]= $item;
					}
				}
				$result = !(
						is_subclass_of($classInfoActive->getName(), $classInfoInactive[0]->getName())
					&&
						is_subclass_of($classInfoActive->getName(), $classInfoInactive[1]->getName())
					&&
						(
							is_subclass_of(
								$classInfoInactive[0]->getName(), $classInfoInactive[1]->getName()
							)
						||
							is_subclass_of(
								$classInfoInactive[1]->getName(), $classInfoInactive[0]->getName()
							)
						)
				);
			}
			else {
				/**
				 * Данный системный класс перекрывается ровно двумя классами.
				 * Смотрим родственные отношения этих классов.
				 *
				 * ПОЯСНЕНИЕ:
				 * Администратор мог уже устранить проблему изменением иерархии наследования:
				 * http://magento-forum.ru/topic/4244/
				 * Однако при этом директивы rewrite остались прежними.
				 * Вообще, не находится удобного способа
				 * одновременно исправить и директивы rewrite, и изменить иерархию наследования.
				 *
				 * Например, если класс Российской сборки (Df_*)
				 * стал родителем второго конфликтующего класса,
				 * то, стало быть, надо удалять директиву rewrite именно модуля Российской сборки,
				 * а такая правка перетрётся обновлением Российской сборки.
				 *
				 * Поэтому сейчас мы перебираем все директивы rewrite и смотрим,
				 * не находятся ли перекрывающие классы в отношении родитель-сын.
				 * Если находятся, и если именно директива сына является активной (важно!),
				 * то мы не считаем данную ситуацию конфликтом.
				 */
				/** @var Df_Admin_Model_ClassInfo $classInfo1 */
				$classInfo1 = $this->getDestinations()->getFirstItem();
				/** @var Df_Admin_Model_ClassInfo $classInfo2 */
				$classInfo2 = $this->getDestinations()->getLastItem();
				/** @var Df_Admin_Model_ClassInfo $classInfoActive */
				/** @var Df_Admin_Model_ClassInfo $classInfoInactive */
				if ($this->isDestinationActive($classInfo1)) {
					$classInfoActive = $classInfo1;
					$classInfoInactive = $classInfo2;
				}
				else {
					$classInfoActive = $classInfo2;
					$classInfoInactive = $classInfo1;
				}
				$result =
						/**
						 * Некоторые конфликты сознательно разрешены:
						 * http://magento-forum.ru/topic/4710/page__view__findpost__p__18177
						 */
						!Df_Admin_Model_ClassRewrite_AllowedConflicts::s()->isAllowed(
							$classInfoActive->getName(), $classInfoInactive->getName()
						)
					&&
						/**
						 * Раньше тут стояло:
						 * $classInfoInactive->getName() !== get_parent_class($classInfoActive->getName())
						 * Новый код позволяет нам отслеживать не только отношения родитель-сын,
						 * но и отношения дед-внук.
						 * Это важно, когда конфликтуют сразу 3 класса,
						 * один из которых - класс Российской сборки.
						 * Например:
							Системный класс «catalog/navigation» типа «block»
							перекрывают конфликтующие между собой классы:
								WP_CustomMenu_Block_Navigation [используется]
								Magebuzz_Catsidebarnav_Block_Catsidebarnav
								Df_Catalog_Block_Navigation
						 * В этой ситуации мы делаем иерархию наследования следующей:
						 * WP_CustomMenu_Block_Navigation
						 * 		extends Magebuzz_Catsidebarnav_Block_Catsidebarnav
						 * 		extends Df_Catalog_Block_Navigation
						 *
						 * rewrite для Magebuzz_Catsidebarnav_Block_Catsidebarnav
						 * мы можем удалить ручной правкой, это нестрашно.
						 * А вот rewrite для Df_Catalog_Block_Navigation удалять нежелательно:
						 * ведь удаление перетрётся при обновлении Российской сборки.
						 *
						 * А оставив rewrite для Df_Catalog_Block_Navigation,
						 * нам нужно отслеживать для rewrite отношения дед-внук
						 * и не считать такие отношения конфликтом.
						 * Вот поэтому мы и используем теперь
						 * @see is_subclass_of() вместо @see get_parent_class()
						 */
						!is_subclass_of($classInfoActive->getName(), $classInfoInactive->getName())
				;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param Df_Admin_Model_ClassInfo $destination
	 * @return bool
	 */
	public function isDestinationActive(Df_Admin_Model_ClassInfo $destination) {
		return $this->getActiveDestinationName() === $destination->getName();
	}

	/** @return string */
	private function getActiveDestinationName() {return $this->getOrigin()->getNameByMf();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__ORIGIN, Df_Admin_Model_ClassInfo::class);
	}
	/** @used-by Df_Admin_Model_ClassRewrite_Collection::itemClass() */

	/** @var string */
	private static $P__ORIGIN = 'origin';

	/**
	 * @used-by Df_Admin_Model_ClassRewrite_Finder::parseRewrites()
	 * @param Df_Admin_Model_ClassInfo $origin
	 * @return Df_Admin_Model_ClassRewrite
	 */
	public static function i(Df_Admin_Model_ClassInfo $origin) {
		return new self(array(self::$P__ORIGIN => $origin));
	}

	/**
	 * @used-by getId()
	 * @used-by Df_Admin_Model_ClassRewrite_Collection::getByOrigin()
	 * @param string $type
	 * @param string $classNameMf
	 * @return string
	 */
	public static function makeId($type, $classNameMf) {return implode('_', array($type, $classNameMf));}
}