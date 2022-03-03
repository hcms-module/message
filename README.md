# 介绍
通知消息模块，比较成熟通知消息解决方案。规定了定义、创建和处理。让你在做通知消息开发的过程中无后顾之忧。


# 安装

```shell
php bin/hyperf.php hcms:install message
```

# 消息定义
## message表结构

| 字段  |    类型     | 说明       |
|:---:|:---------:|:---------|
| title |  string   | 消息标题     |
| content |  string   | 消息内容     |
| target |  string   | 消息来源标识   |
| target_type |  string   | 消息来源标识类型 |
| sender |  string   | 发送者标识    |
| sender_type |  string   | 发送者标识类型  |
| receiver |  string   | 接收者标识    |
| receiver_type |  string   | 接收者标识类型  |
| process_status |    int    | 消息处理状态 0 未处理、1已处理、2处理失败     |
| process_time | timestamp | 消息最后一次处理时间      |
| process_count |    int    | 消息处理次数      |
| class_name |  string   | 实例化类名称      |


# 消息创建
```php
        $message = new SimpleMessage();
        $res = $message->setContent('创建一条简单的消息通知')
            ->setReceiver(1)
            ->setSender(2)
            ->setTarget(3)
            ->createMessage();
```
# 消息处理
所有消息在创建时候都会通过`MessageProcessJob`执行消息处理。
- 执行消息类的 doSender方法
- doSender 拿到消息类定义的 sender 列表逐个进行执行
- 只要有一个sender执行成功，就视为该消息处理成功
```php
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
```

## 异常处理
- 一个消息可以被多个sender进行处理
- 当所有sender都执行异常的时候，处理结果才会作为异常抛出
- 当只有某一个sender执行异常，则异常信息会被记录到 message_error 表中