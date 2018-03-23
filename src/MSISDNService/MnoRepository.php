<?php namespace MSISDNService;

class MnoRepository
{
  /**
   * Instance for singleton design pattern
   *
   * @var MSISDNService|null
   */
  protected static $instance = null;

  /**
   * Flag that signifies if the repository is loaded
   *
   * @var bool
   */
  protected static $loaded = false;

  /**
   * Operators lookup
   *
   * @var array
   */
  private static $operators = null;

  /**
   * Singleton best practice
   */
  protected function __construct() {}
  private function __clone() {}
  private function __wakeup() {}

  /**
   * Singleton
   *
   * @return MnoRepository
   */
  final public static function getInstance() : MnoRepository
  {
    if (static::$instance === null)
      static::$instance = new static();

    return static::$instance;
  }

  /**
   * Getter for operator
   *
   * @param $operatorKey
   * @return array|null
   * @throws \Exception if the repository is not loaded
   */
  public function get($operatorKey) : ?array
  {
    if (!static::$loaded)
      throw new \Exception('Repository data not loaded');

    if (!isset(static::$operators[$operatorKey]))
      return null;

    return static::$operators[$operatorKey];
  }

  /**
   * Loads data from a json file.
   *
   * @param string $path
   *
   * @return bool
   */
  public function loadFile(string $path) : bool
  {
    $dataJson = file_get_contents($path);
    static::$operators = json_decode($dataJson, true);

    static::$loaded = count(static::$operators) > 0;

    return true;
  }
}