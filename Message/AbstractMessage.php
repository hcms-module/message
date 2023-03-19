<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/2/28 10:19
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Message\Message;

use App\Application\Message\Job\MessageProcessJob;
use App\Application\Message\MessageSender\MessageSenderInterface;
use App\Application\Message\Model\Message;
use App\Application\Message\Model\MessageError;
use Hyperf\AsyncQueue\Driver\DriverFactory;
use Hyperf\AsyncQueue\Driver\DriverInterface;
use Hyperf\Di\Annotation\Inject;

abstract class AbstractMessage
{
    protected Message $message;

    protected string $title = '';
    protected string $content = '';
    protected string $target = '';
    protected string $target_type = '';
    protected string $sender = '';
    protected string $sender_type = '';
    protected string $receiver = '';
    protected string $receiver_type = '';
    protected string $class_name = '';

    #[Inject]
    protected DriverFactory $driver_factory;
    protected DriverInterface $driver;

    /**
     * @param Message|null $message
     */
    public function __construct(Message $message = null)
    {
        if (!$message) {
            $message = new Message();
        }
        $this->message = $message;
        $this->class_name = static::class;
        $this->driver = $this->driver_factory->get('default');
    }

    /**
     * 创建消息，并执行消息投递
     *
     * @return bool
     */
    public function createMessage(): bool
    {
        $this->message->title = $this->title;
        $this->message->content = $this->content;
        $this->message->target = $this->target;
        $this->message->target_type = $this->target_type;
        $this->message->sender = $this->sender;
        $this->message->sender_type = $this->sender_type;
        $this->message->receiver = $this->receiver;
        $this->message->receiver_type = $this->receiver_type;
        $this->message->class_name = $this->class_name;
        $this->message->save();
        $this->driver->push(new MessageProcessJob($this->message->message_id));

        return true;
    }

    /**
     * 定义消息发送器
     *
     * @return array
     */
    abstract function getSenders(): array;


    /**
     * 执行发送器操作
     *
     * @return bool
     * @throws \Throwable
     */
    function doSender(): bool
    {
        $this->message->process_count = $this->message->process_count + 1;
        $senders = $this->getSenders();
        $res = false;
        if (!empty($senders)) {
            $throwable = null;
            foreach ($senders as $sender_class) {
                if (class_exists($sender_class)) {
                    $sender = new $sender_class();
                    if ($sender instanceof MessageSenderInterface) {
                        try {
                            $res = $sender->handler($this);
                        } catch (\Throwable $t) {
                            //记录异常信息
                            MessageError::create([
                                'message_id' => $this->message->message_id,
                                'process_class' => get_class($sender),
                                'error_msg' => $t->getMessage(),
                                'error_detail' => substr($t->getTraceAsString(), 0, 2000),
                            ]);
                            $throwable = $t;
                        }
                    }
                }
            }
            /**
             * 如果所有的执行都是异常才抛出异常，只要有一个处理是成功就为成功
             */
            if (!$res && $throwable !== null) {
                $this->message->process_status = Message::PROCESS_STATUS_FAILED;
                $this->message->process_time = time();
                $this->message->save();
                throw $throwable;
            }
        }


        $this->message->process_status = Message::PROCESS_STATUS_SUCCESS;
        $this->message->process_time = time();
        $this->message->save();

        return true;
    }

    /**
     * @return Message|null
     */
    public function getMessage(): ?Message
    {
        return $this->message;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @param mixed $target
     * @return $this
     */
    public function setTarget($target = 0): self
    {
        $this->target = (string)$target;

        return $this;
    }

    /**
     * @param string $target_type
     */
    public function setTargetType(string $target_type): void
    {
        $this->target_type = $target_type;
    }

    /**
     * @param mixed $sender
     * @return $this
     */
    public function setSender($sender = 0): self
    {
        $this->sender = (string)$sender;

        return $this;
    }


    /**
     * @param string $sender_type
     * @return $this
     */
    public function setSenderType(string $sender_type): self
    {
        $this->sender_type = $sender_type;

        return $this;
    }

    /**
     * @param mixed $receiver
     * @return $this
     */
    public function setReceiver($receiver = 0): self
    {
        $this->receiver = (string)$receiver;

        return $this;
    }


    /**
     * @param string $receiver_type
     * @return $this
     */
    public function setReceiverType(string $receiver_type): self
    {
        $this->receiver_type = $receiver_type;

        return $this;
    }

    /**
     * @param string $class_name
     * @return $this
     */
    public function setClassName(string $class_name): self
    {
        $this->class_name = $class_name;

        return $this;
    }
}