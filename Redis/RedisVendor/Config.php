<?php
/********************************************************************************
 * Copyright: PhpStorm - PersonalTest - Config.php
 * Author:    WuHui
 * Date:      2018-05-31 21:14
 * Desc:      功能描述
 *******************************************************************************/

namespace RedisVendor;


class Config
{
    /**
     * @desc   本机redis的连接配置
     * @return array
     * ```
     * 配置字段说明：
     * master        主库
     * slave         从库
     * ip            服务所在机器IP
     * port          服务所在机器提供服务的端口
     * databases     库下标
     * ```
     */
    public static function LocalhostRedisConf()
    {
        return [
            'master' => [
                'ip' => '127.0.0.1',
                'port' => '6379',
                'databases' => '0',
            ],
            'slave' => [
                [
                    'ip' => '127.0.0.1',
                    'port' => '6379',
                    'databases' => '1',
                ],
                [
                    'ip' => '127.0.0.1',
                    'port' => '6379',
                    'databases' => '2',
                ]
            ]
        ];
    }
}