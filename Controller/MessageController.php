<?php

declare(strict_types=1);

namespace App\Application\Message\Controller;

use App\Annotation\Api;
use App\Annotation\View;
use App\Application\Admin\Middleware\AdminMiddleware;
use App\Application\Message\Job\MessageProcessJob;
use App\Application\Message\Message\SimpleMessage;
use App\Application\Message\Model\Message;
use App\Controller\AbstractController;
use Hyperf\AsyncQueue\Driver\DriverFactory;
use Hyperf\AsyncQueue\Driver\DriverInterface;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\PostMapping;

#[Middleware(AdminMiddleware::class)]
#[Controller(prefix: "/message/message")]
class MessageController extends AbstractController
{
    protected DriverInterface $driver;

    public function __construct(DriverFactory $driver_factory)
    {
        $this->driver = $driver_factory->get('default');
    }

    /**
     * 创建测试消息
     */
    #[Api]
    #[PostMapping("create")]
    public function createMessage()
    {
        $message = new SimpleMessage();
        $res = $message->setContent('创建一条简单的消息通知')
            ->setReceiver(1)
            ->setSender(2)
            ->setTarget(3)
            ->createMessage();

        return $res ? $this->returnSuccessJson() : $this->returnErrorJson();
    }

    /**
     * 重新执行消息处理
     */
    #[Api]
    #[PostMapping("handle")]
    public function messageHandle()
    {
        $message_id = (int)$this->request->post('message_id', 0);
        $message = Message::findOrNew($message_id);
        if (!$message->message_id) {
            return $this->returnErrorJson('找不到该记录');
        }

        $res = $this->driver->push(new MessageProcessJob($message->message_id));

        return $res ? $this->returnSuccessJson() : $this->returnErrorJson();
    }

    /**
     * 删除消息记录
     */
    #[Api]
    #[PostMapping("delete")]
    public function messageDelete()
    {
        $message_id = $this->request->input('message_id', 0);
        $message = Message::findOrNew($message_id);
        if (!$message->message_id) {
            return $this->returnErrorJson('找不到该记录');
        }

        return $message->delete() ? $this->returnSuccessJson() : $this->returnErrorJson();
    }

    /**
     * 消息列表
     */
    #[Api]
    #[GetMapping("lists")]
    public function lists()
    {
        $_where = $this->request->input('where', []);
        $where = [];
        foreach ($_where as $key => $item) {
            if ($item != '') {
                $where[] = [
                    $key,
                    '=',
                    $item
                ];
            }
        }
        $lists = Message::where($where)
            ->with('lastErrorInfo')
            ->orderBy('message_id', 'DESC')
            ->paginate();

        return compact('lists');
    }

    #[View]
    #[GetMapping("")]
    public function index()
    {
    }
}
