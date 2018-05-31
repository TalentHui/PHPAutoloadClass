<?php
/********************************************************************************
 * Copyright: PhpStorm - ClassLoader.php
 * Author:    WuHui
 * Date:      2018-04-04 15:51
 * Desc:      自动加载类
 * <pre>
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * | Project 工程
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * | Libraries |~~~~~~~~~~~~~~~~~~~~
 *             | Module |~~~~~~~~~~~
 *             |        | Model.php
 *                      <?php
 *                      namespace Libraries\Module;
 *                      class Model
 *                      {
 *                          public static function run()
 *                          {
 *                              echo __METHOD__ . PHP_EOL;
 *                          }
 *                      }
 *             | ClassLoader.php
 * | Public    |~~~~~~~~~~~~~~~~~~~~
 *             | index.php
 *               <?php
 *               define('ROOT_DIR', realpath(dirname(__FILE__) . '/../') . DIRECTORY_SEPARATOR);
 *
 *               include '../Libraries/ClassLoader.php';
 *               $ClassLoader = new ClassLoader();
 *               $ClassLoader::registerNamespace('Libraries', ROOT_DIR);
 *               $ClassLoader::register();
 *
 *               \Libraries\Module\Model::run();
 *
 *               $ClassLoader::registerNamespace('.', ROOT_DIR . 'Root');
 *
 *               Two::run();
 * | Root      |~~~~~~~~~~~~~~~~~~~~
 *             | One.php
 *               <?php
 *               class One
 *               {
 *
 *               }
 *             | Two.php
 *               <?php
 *               class Two extends One
 *               {
 *                   public static function run()
 *                   {
 *                       echo __METHOD__ . PHP_EOL;
 *                   }
 *               }
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 *
 * <pre>
 *******************************************************************************/

class ClassLoader
{
    /**
     * Namespaces root path
     *
     * @var array
     * <pre>
     * var_dump(self::$_l_namespace)
     *
     * array(
     *   '\Vendor'  => array(
     *      '/data/webdir/project_directory/Vendor/'
     *   )
     * )
     * <pre>
     */
    protected static $_l_namespace = array();

    /**
     * Without open debug ?
     *
     * @var bool
     */
    protected static $_l_open_debug = false;

    /**
     * Without use APC cache ?
     *
     * @var bool
     */
    protected static $_l_apc = false;

    /**
     * APC cache prefix
     *
     * @var null
     */
    protected static $_l_apc_prefix = null;

    /**
     * Set autoloader to use Apc cache.
     *
     * @param  bool $open_debug Without open debug print
     * @param  bool $apc        Without open apc cache
     * @param  null $apc_prefix Apc cache prefix
     */
    public function __construct($open_debug = false, $apc = false, $apc_prefix = null)
    {
        if (!function_exists('apc_fetch')) {
            $apc = false;
        }

        self::$_l_open_debug = $open_debug;
        self::$_l_apc = $apc;
        self::$_l_apc_prefix = $apc_prefix;
    }

    /**
     * Register a namespace
     *
     * @param  string $namespace   Namespace eq . sign no namespace class load
     * @param  array|string $paths Directory path include Namespace or file
     */
    public static function registerNamespace($namespace, $paths)
    {
        if (!empty(self::$_l_namespace[$namespace])) {
            self::$_l_namespace[$namespace] = array_merge(self::$_l_namespace, (array) $paths);
        } else {
            self::$_l_namespace[$namespace] = (array) $paths;
        }
    }

    /**
     * Register this instance as an autoloader
     *
     * @param  bool $exception Without throw exception
     * @param  bool $prepend   Without register queue head
     */
    public static function register($exception = true, $prepend = false)
    {
        spl_autoload_register("ClassLoader::loadClass", $exception, $prepend);
    }

    /**
     * Loads the given class, definition or interface
     *
     * @param  string $class The name of the class
     */
    public static function loadClass($class)
    {
        if ((true === self::$_l_apc && ($file = self::findFileInApc($class))) || ($file = self::findFile($class))) {
            require_once "ClassLoader.php";
        }
    }

    /**
     * Loads the given class or interface in APC
     *
     * @param  $class
     * @return string
     */
    public static function findFileInApc($class)
    {
        if (false === $file = apc_fetch(self::$_l_apc_prefix . $class)) {
            apc_store(self::$_l_apc_prefix . $class, $file = self::findFile($class));
        }

        return $file;
    }

    /**
     * Find class in namespace or definitions directories
     *
     * @param  $class
     * @return string
     */
    public static function findFile($class)
    {
        // Remove first backslash
        if ('\\' == $class['0']) {
            $class = substr($class, 1);
        }

        if (self::$_l_open_debug) {
            echo $class . PHP_EOL;
        }

        $file = null;

        // Libraries name include namespace use if without else
        if (false !== $pos = strrpos($class, '\\')) {

            // Namespace of class name
            $namespace = substr($class, 0, $pos);

            if (self::$_l_open_debug) {
                echo $namespace . PHP_EOL;
            }

            // Iterate in normal namespaces
            foreach (self::$_l_namespace as $ns => $dirs) {

                // Don't interfere with other autoloader
                if (0 !== strpos($namespace, $ns)) {
                    continue;
                }

                foreach ($dirs as $dir) {

                    $class_name = substr($class, $pos + 1);

                    if (self::$_l_open_debug) {
                        echo "Directory Path: {$dir}" . PHP_EOL;
                        echo "Class Name: {$class_name}" . PHP_EOL;
                    }

                    $location_file = rtrim($dir, DIRECTORY_SEPARATOR);
                    $location_file .= DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $namespace);
                    $location_file .= DIRECTORY_SEPARATOR . $class_name . '.php';

                    if (self::$_l_open_debug) {
                        echo "Location File: {$location_file}" . PHP_EOL;
                    }

                    if (file_exists($location_file)) {
                        $file = $location_file;
                    }
                }
            }
        } else {

            $no_namespace_directory = empty(self::$_l_namespace['.']) ? '' : self::$_l_namespace['.'];

            foreach ($no_namespace_directory as $dir) {

                $location_file = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $class . '.php';

                if (self::$_l_open_debug) {
                    echo "Location File: {$location_file}" . PHP_EOL;
                }

                if (file_exists($location_file)) {
                    $file = $location_file;
                }
            }
        }

        return $file;
    }
}