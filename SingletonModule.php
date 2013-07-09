<?php
namespace samson\core;

/**
 * Модуль имеющий единственный свой экземпляр c расширенными возможностями 
 * подключения к фреймворку. 
 * 
 * Удобен для использования во внешних модулях у которых всего один внешний главный
 * класс и это позволяет всю его логику внести в SingletonModule не создавая 2 отдельных
 * файла и класса.
 *
 * @author Vitaly Iegorov <vitalyiegorov@gmail.com> 
 * @version 0.1
 */
class SingletonModule extends ExternalModule
{
	/**
	 * Коллекция экземпляров классов 
	 * @var SingletonModule 
	 */
	private static $_factory = array();	
	
	/**
	 * @param $class Classname for getting instance
	 * @return SingletonModule 
	 */
	public static function getInstance( $class = null )
	{		
		// Получим класс из которого был вызван метод
		$class = isset( $class ) ? $class : 
			function_exists('get_called_class') ? get_called_class() : e('Classname not specified', E_SAMSON_FATAL_ERROR);
		
		// Вернем единственный экземпляр
		return self::$_factory[ $class ];
	}
	
	/** Конструктор */
	public function __construct( $core_id, $path = NULL, array $params = NULL )
	{		
		// Получим имя класса
		$class = get_class( $this );
			
		// Проверка на Singleton
		if( !isset(self::$_factory[ $class ]) ) self::$_factory[ $class ] = $this;
		else e('Попытка создания дополнительного объекта для ##', E_SAMSON_FATAL_ERROR, __CLASS__ );
		
		// Вызовем родительский конструктор
		parent::__construct( $core_id, $path, $params );
	}
	
	/** @see Module::duplicate() */
	public function & duplicate( $id, $class_name = null )
	{
		// Вызовем родительский метод
		return parent::duplicate( $id, '\samson\core\ModuleConnector' );
	}

	/** Обработчик сериализации объекта */
	public function __sleep(){	return array_merge( parent::__sleep(), array_keys(get_object_vars($this)));}
	/** Обработчик десериализации объекта */
	public function __wakeup(){	parent::__wakeup();	self::$_factory[ get_class($this) ] = $this; }
}