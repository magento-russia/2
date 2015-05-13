<?php
class Df_1C_Model_Cml2_Export_Processor_Order extends Df_1C_Model_Cml2_Export_Processor {
	/** @return Df_1C_Model_Cml2_Export_Processor_Order */
	public function process() {
		$this->getDocument()
			->importArray(
				$this->getDocumentData_Order()
				,$wrapInCData =
					array(
						'Ид'
						,'Комментарий'
						,'Наименование'
						,'Описание'
						,'Представление'

					)
			)
		;
		return $this;
	}

	/** @return Df_Sales_Model_Order_Address */
	private function getAddress() {
		return $this->getCustomer()->getMergedAddressWithShippingPriority();
	}
	
	/** @return Df_1C_Model_Cml2_Export_Data_Entity_Customer */
	private function getCustomer() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_1C_Model_Cml2_Export_Data_Entity_Customer::i($this->getOrder());
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Varien_Simplexml_Element */
	private function getDocument() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * Обратите внимание, что
			 * @sww SimpleXMLElement::addChild создаёт и возвращает не просто @see SimpleXMLElement,
			 * как говорит документация, а объект класса родителя.
			 * Поэтому в нашем случае addChild создаст объект @see Df_Varien_Simplexml_Element.
			 */
			$this->{__METHOD__} = $this->getSimpleXmlElement()->addChild('Документ');
		}
		return $this->{__METHOD__};
	}

	/** @return mixed[] */
	private function getDocumentData_Customer() {
		/** @var mixed[] $result */
		$result =
			array(
				'Ид' => $this->getCustomer()->getId()
				,'Наименование' => $this->getCustomer()->getNameShort()
				,'Роль' => 'Покупатель'
				,'ПолноеНаименование' => $this->getCustomer()->getNameFull()
				,'Фамилия' => $this->getCustomer()->getNameLast()
				,'Имя' => $this->getCustomer()->getNameFirst()
				,'Отчество' => $this->getCustomer()->getNameMiddle()
				,'ДатаРождения' => $this->getCustomer()->getDateOfBirthAsString()
				,'Пол' => $this->getCustomer()->getGenderAsString()
				,'ИНН' => $this->getCustomer()->getInn()
				,'КПП' => ''
				,'АдресРегистрации' => $this->getDocumentData_CustomerAddress()
				,'Адрес' => $this->getDocumentData_CustomerAddress()
				,'Контакты' => $this->getDocumentData_CustomerContacts()
			)
		;
		return $result;
	}

	/** @return mixed[] */
	private function getDocumentData_CustomerAddress() {
		/** @var mixed[] $result */
		$result =
			array(
				'Представление' =>
					rm_cdata(df_nts(
						$this->getAddress()->format(
							Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_TEXT
						)
					))
				,'АдресноеПоле' =>
					array(
						array(
							'Тип' => 'Почтовый индекс'
							,'Значение' => $this->getAddress()->getPostcode()
						)
						,array(
							'Тип' => 'Улица'
							,'Значение' =>
								rm_cdata(
									is_array($this->getAddress()->getStreetFull())
									? implode("\r\n", $this->getAddress()->getStreetFull())
									: $this->getAddress()->getStreetFull()
								)
						)
						,array(
							'Тип' => 'Страна'
							,'Значение' => $this->getAddress()->getCountryModel()->getName()
						)
						,array(
							'Тип' => 'Регион'
							,'Значение' => $this->getAddress()->getRegion()
						)
						,array(
							'Тип' => 'Район'
							,'Значение' => ''
						)
						,array(
							'Тип' => 'Населенный пункт'
							,'Значение' => $this->getAddress()->getCity()
						)
						,array(
							'Тип' => 'Город'
							,'Значение' => $this->getAddress()->getCity()
						)
						,array(
							'Тип' => 'Улица'
							,'Значение' => ''
						)
						,array(
							'Тип' => 'Дом'
							,'Значение' => ''
						)
						,array(
							'Тип' => 'Корпус'
							,'Значение' => ''
						)
						,array(
							'Тип' => 'Квартира'
							,'Значение' => ''
						)
					)
			)
		;
		return $result;
	}

	/** @return mixed[] */
	private function getDocumentData_CustomerContacts() {
		/** @var mixed[] $result */
		$result =
			array(
				'Контакт' =>
					array(
						array(
							'Тип' => 'ТелефонРабочий'
							,'Значение' => $this->getAddress()->getTelephone()
						)
						,array(
							'Тип' => 'Почта'
							,'Значение' => $this->getCustomer()->getEmail()
						)
					)
			)
		;
		return $result;
	}

	/** @return mixed[] */
	private function getDocumentData_Discounts() {
		/** @var mixed[] $result */
		$result =
			array(
				array(
					'Наименование' => 'Скидка'
					,'УчтеноВСумме' => rm_bts(true)
					,'Сумма' =>
						df_h()->_1c()->formatMoney(
							abs($this->getOrder()->getDiscountAmount())
						)
				)
			)
		;
		/** @var float $rewardAmount */
		$rewardAmount =
			rm_float(
				$this->getOrder()->getData(Df_Sales_Model_Order::P__REWARD_CURRENCY_AMOUNT)
			)
		;
		if (0 < $rewardAmount) {
			$result[]=
				array(
					'Наименование' => 'Бонусная скидка'
					,'УчтеноВСумме' => rm_bts(false)
					,'Сумма' => df_h()->_1c()->formatMoney($rewardAmount)
				)
			;
		}

		/** @var float $customerBalanceAmount */
		$customerBalanceAmount =
			rm_float(
				$this->getOrder()->getData(
					Df_Sales_Model_Order::P__CUSTOMER_BALANCE_AMOUNT
				)
			)
		;
		if (0 < $customerBalanceAmount) {
			$result[]=
				array(
					'Наименование' => 'Оплата с личного счёта'
					,'УчтеноВСумме' => rm_bts(false)
					,'Сумма' => df_h()->_1c()->formatMoney($customerBalanceAmount)
				)
			;
		}
		return $result;
	}

	/** @return mixed[] */
	private function getDocumentData_Order() {
		/** @var mixed[] $result */
		$result =
			array(
				'Ид' => $this->getOrder()->getId()
				,'Номер' => $this->getOrder()->getIncrementId()
				,'Дата' =>
					df_dts(
						$this->getOrder()->getCreatedAtStoreDate()
						,Df_1C_Model_Cml2_SimpleXml_Generator_Document::DATE_FORMAT
					)
				,'ХозОперация' => 'Заказ товара'
				,'Роль' => 'Продавец'
				,'Валюта' =>
					df_h()->_1c()->cml2()->convertCurrencyCodeTo1CFormat(
						$this->getOrder()->getOrderCurrencyCode()
					)
				,'Курс' => 1
				,'Сумма' => df_h()->_1c()->formatMoney($this->getOrder()->getGrandTotal())
				,'Контрагенты' =>
					array(
						'Контрагент' => $this->getDocumentData_Customer()
					)
				/**
				 * Раньше здесь использовался формат Zend_Date::TIME_MEDIUM.
				 * Однако при обмене заказами с конфигурацией «Управление торговлей для Украины»
				 * это приводило к сбою функции ОбработатьДатуВремяCML.
				 */
				,'Время' => df_dts($this->getOrder()->getCreatedAtStoreDate(), 'HH:mm:ss')
				,'Налоги' =>
					array(
						'Налог' =>
							array(
								'Наименование' => 'Совокупный налог'
								,'УчтеноВСумме' => rm_bts(true)
								,'Сумма' =>
									df_h()->_1c()->formatMoney(
										$this->getOrder()->getTaxAmount()
									)
							)
					)
				,'Скидки' =>
					array(
						'Скидка' => $this->getDocumentData_Discounts()
					)
				,'Товары' =>
					array(
						'Товар' => $this->getDocumentData_OrderItems()
					)
					,'ЗначенияРеквизитов' =>
						array(
							'ЗначениеРеквизита' => $this->getDocumentData_OrderProperties()
						)
				,'Комментарий' => $this->getOrderComments()
			)
		;
		return $result;
	}

	/** @return mixed[] */
	private function getDocumentData_OrderItems() {
		/** @var mixed[] $result */
		$result = array();
		foreach ($this->getOrder()->getItemsCollection() as $item) {
			/** @var Mage_Sales_Model_Order_Item $item */
			if (Mage_Catalog_Model_Product_Type::TYPE_SIMPLE === $item->getProductType()) {
				$result[]= Df_1C_Model_Cml2_Export_Processor_Order_Item::i($item)->getDocumentData();
			}
		}
		if (0 < $this->getOrder()->getShippingAmount()) {
			/**
			 * Используем тот же трюк, что и 1С-Битрикс:
			 * указываем стоимость доставки отдельной строкой заказа
			 */
			$result[]=
				array(
					'Ид' => 'ORDER_DELIVERY'
					,'Наименование' => 'Доставка заказа'
					,'БазоваяЕдиница' =>
						array(
							Df_Varien_Simplexml_Element::KEY__ATTRIBUTES =>
								array(
									'Код' => '796'
									,'НаименованиеПолное' => 'Штука'
									,'МеждународноеСокращение' => 'PCE'
								)
							,Df_Varien_Simplexml_Element::KEY__VALUE => 'шт'
						)
					,'ЦенаЗаЕдиницу' =>
						df_h()->_1c()->formatMoney(
							$this->getOrder()->getShippingAmount()
						)
					,'Количество' => 1
					,'Сумма' =>
						df_h()->_1c()->formatMoney(
							$this->getOrder()->getShippingAmount()
						)
					,'ЗначенияРеквизитов' =>
						array(
							'ЗначениеРеквизита' =>
								array(
									array(
										'Наименование' => 'ВидНоменклатуры'
										,'Значение' => 'Услуга'
									)
									,array(
										'Наименование' => 'ТипНоменклатуры'
										,'Значение' => 'Услуга'
									)
								)
						)

				)
			;
		}
		$result = df_clean($result);
		return $result;
	}

	/** @return string[] */
	private function getDocumentData_OrderProperties() {
		/** @var string[] $result */
		$result = array();
		if (false !== $this->getOrder()->getPayment()) {
			$result[]=
				array(
					'Наименование' => 'Метод оплаты'
					,'Значение' => $this->getOrder()->getPayment()->getMethodInstance()->getTitle()
				)
			;
		}
		$result[]=
			array(
				'Наименование' => 'Заказ оплачен'
				,'Значение' => rm_bts(0 >= $this->getOrder()->getTotalDue())
			)
		;
		$result[]=
			array(
				'Наименование' => 'Способ доставки'
				,'Значение' => $this->getOrder()->getShippingDescription()
			)
		;
		$result[]=
			array(
				'Наименование' => 'Доставка разрешена'
				,'Значение' => rm_bts($this->getOrder()->canShip())
			)
		;
		$result[]=
			array(
				'Наименование' => 'Отменен'
				,'Значение' => rm_bts($this->getOrder()->isCanceled())
			)
		;
		$result[]=
			array(
				'Наименование' => 'Финальный статус'
				,'Значение' =>
					rm_bts(Mage_Sales_Model_Order::STATE_COMPLETE === $this->getOrder()->getState())
			)
		;
		$result[]=
			array(
				'Наименование' => 'Статус заказа'
				,'Значение' =>
					implode(
						' / '
						,array(
							$this->getOrder()->getState()
							,$this->getOrder()->getStatus()
						)
					)
			)
		;
		$result[]=
			array(
				'Наименование' => 'Дата изменения статуса'
				,'Значение' => $this->getOrder()->getUpdatedAt()
			)
		;
		$result[]=
			array(
				'Наименование' => 'Сайт'
				,'Значение' => $this->getOrder()->getStore()->getName()
			)
		;
		return $result;
	}

	/** @return Df_Sales_Model_Order */
	private function getOrder() {
		return $this->cfg(self::P__ORDER);
	}

	/** @return string */
	private function getOrderComments() {
		/** @var string[] $comments */
		$comments = array();
		foreach ($this->getOrder()->getAllStatusHistory() as $historyItem) {
			/** @var Mage_Sales_Model_Order_Status_History $historyItem */
			if ($historyItem->getComment()) {
				$comments[]=
					implode("\r\n", array($historyItem->getCreatedAt(), $historyItem->getComment()))
				;
			}
		}
		return implode("\r\n\r\n", $comments);
	}

	/** @return Df_Varien_Simplexml_Element */
	private function getSimpleXmlElement() {
		return $this->cfg(self::P__SIMPLE_XML_ELEMENT);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__ORDER, Df_Sales_Model_Order::_CLASS)
			->_prop(self::P__SIMPLE_XML_ELEMENT, Df_Varien_Simplexml_Element::_CLASS)
		;
	}
	const _CLASS = __CLASS__;
	const P__ORDER = 'order';
	const P__SIMPLE_XML_ELEMENT = 'simple_xml_element';
	/**
	 * @static
	 * @param Df_Sales_Model_Order $order
	 * @param Df_Varien_Simplexml_Element $xml
	 * @return Df_1C_Model_Cml2_Export_Processor_Order
	 */
	public static function i(Df_Sales_Model_Order $order, Df_Varien_Simplexml_Element $xml) {
		return new self(array(self::P__ORDER => $order, self::P__SIMPLE_XML_ELEMENT => $xml));
	}
}