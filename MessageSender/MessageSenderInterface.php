<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/2/28 11:51
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Message\MessageSender;


use App\Application\Message\Message\AbstractMessage;

interface MessageSenderInterface
{
    public function handler(AbstractMessage $message): bool;
}