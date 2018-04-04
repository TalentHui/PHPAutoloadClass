# PHPAutoloadClass - 自动加载类
***
* 类的自动加载函数，支持加载第三方类库

```
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * | Project 工程
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * | Libraries |~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 *             | Module |~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
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
 * | Public    |~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
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
 *               // 无命名空间的类的加载，第一个参数必须为 .
                 // 一个命名空间可以对应多个路径
 *               $ClassLoader::registerNamespace('.', ROOT_DIR . 'Root');
 *
 *               Two::run();
 * | Root      |~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
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
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
```
* 运行后的输出结果
```
Windows@Tmp MINGW64 /d/Project/Public (master_21105)
$ php index.php
Libraries\Module\Model::run
Two::run
```

