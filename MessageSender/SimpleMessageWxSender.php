<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/2/28 15:50
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Message\MessageSender;


use App\Application\Message\Message\AbstractMessage;

class SimpleMessageWxSender implements MessageSenderInterface
{
    public function handler(AbstractMessage $message): bool
    {
        var_dump('do wx sender');

        return true;
    }
}