<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/2/28 13:52
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Message\Job;

use App\Application\Message\Message\AbstractMessage;
use App\Application\Message\Model\Message;
use Hyperf\AsyncQueue\Job;

class MessageProcessJob extends Job
{
    protected $maxAttempts = 3;
    public int $message_id;

    /**
     * @param int $message_id
     */
    public function __construct(int $message_id) { $this->message_id = $message_id; }

    /**
     * @throws \Throwable
     */
    public function handle()
    {
        $message = Message::findOrNew($this->message_id);
        if ($message->message_id) {
            if (class_exists($message->class_name)) {
                $class_name = new $message->class_name($message);
                if ($class_name instanceof AbstractMessage) {
                    $class_name->doSender();
                }
            }
        }
    }
}