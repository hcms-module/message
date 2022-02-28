<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/2/28 10:45
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Message\Message;

use App\Application\Message\MessageSender\SimpleMessageErrorSender;
use App\Application\Message\MessageSender\SimpleMessageSmsSender;
use App\Application\Message\MessageSender\SimpleMessageWxSender;

class SimpleMessage extends AbstractMessage
{
    protected string $target_type = 'simple_id';
    protected string $receiver_type = 'user_id';
    protected string $sender_type = 'send_id';
    protected string $title = '简单消息通知';


    function getSenders(): array
    {
        return [
            SimpleMessageWxSender::class,
            SimpleMessageErrorSender::class,
            SimpleMessageSmsSender::class
        ];
    }
}