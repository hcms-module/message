<?php

declare(strict_types=1);

return [
    'name' => 'Message',
    'require' => [
        'hcms_version' => '3.0.0',
        'composer' => [],
        'module' => []
    ],
    'version' => '3.0.0',
    'install_check' => function () {
        //检查是否开启队列服务
        $processes = config('processes');

        if (!in_array(\Hyperf\AsyncQueue\Process\ConsumerProcess::class, $processes)) {
            throw new ErrorException('请到 processes 配置中开启 ' . '\Hyperf\AsyncQueue\Process\ConsumerProcess::class');
        }

        return true;
    }
];