<?php namespace Singleton;

class Singleton
{
  protected static $instance = null;

  /**
   * The constructor __construct() is declared as protected to prevent creating
   * a new instance outside of the class via the new operator.
   *
   * Singleton constructor.
   */
  protected function __construct () {}

  /**
   * The magic method __clone() is declared as private to prevent cloning of an instance
   * of the class via the clone operator
   */
  final private function __clone() {}

  /**
   * The magic method __wakeup() is declared as private to prevent unserializing
   * of an instance of the class via the global function unserialize()
   */
  final private function __wakeup() {}

  final public static function getInstance()
  {
    if (static::$instance === null)
      static::$instance = new static();

    return static::$instance;
  }
}