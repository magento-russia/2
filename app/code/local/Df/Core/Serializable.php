<?php
/**
 * @used-by rm_serialize()
 * @used-by rm_unserialize()
 * Поддержка классом данного интерфейса
 * позволяет объектам этого класса выполнять  некие действия до сериализации,
 * посое сериализации, и после десериализации.
 */
interface Df_Core_Serializable {
	/**
	 * В качестве параметра передаётся результат предыдущего вызова @see serializeBefore().
	 * @used-by rm_serialize()
	 * @param array(string => mixed) $data
	 * @return void
	 */
	public function serializeAfter(array $data);
	/**
	 * Результат этого метода будет после сериализации передан методу @see serializeAfter().
	 * Это позволяет избежать сериализации некоторых свойств объекта
	 * (для этого надо скопировать эти свойства в контейнер,
	 * установить эти свойства в null в самом объекте,
	 * а после сериализации восстановить эти свойства в объекте из контейнера).
	 * @used-by rm_serialize()
	 * @return array(string => mixed)
	 */
	public function serializeBefore();
	/**
	 * @used-by rm_unserialize()
	 * @return void
	 */
	public function unserializeAfter();
}