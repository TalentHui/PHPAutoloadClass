<?php
/********************************************************************************
 * Copyright: PhpStorm - PersonalTest - Pool.php
 * Author:    WuHui
 * Date:      2018-05-31 21:14
 * Desc:      PHP 扩展Redis连接池
 * ```
 * 实现的功能：
 * 1、能建立多个redis连接对象，区分主从
 * 2、能区分redis的操作指令，对部分命令进行限制
 * 3、配置中没有主库的配置，抛出异常
 * 4、使用连接对象时如果连接对象断开，自动连接一定次数后【3次】，如果还是失败，抛出异常
 * ```
 *******************************************************************************/

namespace RedisVendor;


class Pool
{
    /**
     * @desc  redis connect object pool
     * @var   array
     * ```
     * var_dump(self::$_s_redis_connect_pool);
     *
     *
     * [
     *      crc32(json_encode($redis_connect_conf)) => [
     *          'master' => [
     *              RedisConnectObject1,
     *              RedisConnectObject2,
     *              ...
     *              RedisConnectObjectN,
     *          ],
     *          'slave' => [
     *              RedisConnectObject1,
     *              RedisConnectObject2,
     *              ...
     *              RedisConnectObjectN,
     *          ]
     *      ]
     * [
     * ```
     */
    protected static $_s_redis_connect_pool = [];

    /** @var array Redis连接池配置 */
    protected static $_s_redis_connect_pool_conf = [];

    /** @var int 设置主库对象数量 */
    protected static $_s_master_object_number = 1;

    /** @var int 设置从库对象数量 */
    protected static $_s_slave_object_number = 1;

    /** @var string 最后一次错误信息 */
    protected static $_s_last_error = '';

    /**
     * @desc  设置主库连接对象数量
     * @param int $create_object_number
     */
    public function SetMasterNumber($create_object_number = 1)
    {
        $create_object_number = (empty($create_object_number) || $create_object_number < 1) ? 1 : intval($create_object_number);
        self::$_s_master_object_number = $create_object_number;
    }

    /**
     * @desc  设置从库连接对象数量
     * @param int $create_object_number
     */
    public function SetSlaveNumber($create_object_number = 1)
    {
        $create_object_number = (empty($create_object_number) || $create_object_number < 1) ? 1 : intval($create_object_number);
        self::$_s_slave_object_number = $create_object_number;
    }

    /**
     * @desc  创建Redis连接配置
     * @param array $redis_conf
     * @return bool|int
     */
    public static function CreateRedisConnectObject(array $redis_conf = array())
    {
        try {
            if (empty($redis_conf['master'])) {
                throw new \Exception('Master Conf Not NULL');
            }

            $object_sign = crc32(json_encode($redis_conf));

            // 建立主库对象
            $master_redis_conf = $redis_conf['master']['0'];
            $master_redis_ip = empty($master_redis_conf['ip']) ? '127.0.0.1' : $master_redis_conf['ip'];
            $master_redis_port = empty($master_redis_conf['port']) ? '6379' : $master_redis_conf['port'];
            $master_redis_database = empty($master_redis_conf['database']) ? 0 : $master_redis_conf['database'];
            $master_redis_timeout = empty($master_redis_conf['timeout']) ? 1 : $master_redis_conf['timeout'];

            for ($init_master_number = 1; $init_master_number <= self::$_s_master_object_number; $init_master_number++) {
                $master_redis = new \Redis();
                $master_redis->connect($master_redis_ip, $master_redis_port, $master_redis_timeout);
                $master_redis->select($master_redis_database);
                self::$_s_redis_connect_pool[$object_sign]['master'][] = $master_redis;
                unset($master_redis);
            }

            // 建立从库对象
            if (!empty($redis_conf['slave'])) {
                $slave_conf_count = count($redis_conf['slave']);

                for ($init_slave_number = 1; $init_slave_number <= self::$_s_slave_object_number; $init_slave_number++) {
                    $slave_conf_index = $init_slave_number % $slave_conf_count;
                    $slave_redis_conf = $redis_conf['slave'][$slave_conf_index];

                    $master_redis_ip = empty($slave_redis_conf['ip']) ? '127.0.0.1' : $slave_redis_conf['ip'];
                    $master_redis_port = empty($slave_redis_conf['port']) ? '6379' : $slave_redis_conf['port'];
                    $master_redis_database = empty($slave_redis_conf['database']) ? 0 : $slave_redis_conf['database'];
                    $master_redis_timeout = empty($slave_redis_conf['timeout']) ? 1 : $slave_redis_conf['timeout'];

                    $master_redis = new \Redis();
                    $master_redis->connect($master_redis_ip, $master_redis_port, $master_redis_timeout);
                    $master_redis->select($master_redis_database);
                    self::$_s_redis_connect_pool[$object_sign]['slave'][] = $master_redis;
                    unset($master_redis);
                }
            }

            self::$_s_redis_connect_pool_conf[$object_sign] = $redis_conf;
        } catch (\Exception $e) {
            $object_sign = false;
            self::$_s_last_error = $e->getMessage();
        }

        return $object_sign;
    }

    public static function GetRedisConnectObject($object_sign)
    {
        try {
            if (self::$_s_redis_connect_pool[$object_sign]) {

            }
        } catch (\Exception $e) {

        }
    }
}