<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateMessageTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('message', function (Blueprint $table) {
            $table->bigIncrements('message_id');
            $table->string('title', 128)
                ->nullable(false)
                ->default('')
                ->comment('消息标题');
            $table->string('content', 1024)
                ->nullable(false)
                ->default('')
                ->comment('消息内容，不建议存储过长信息');
            $table->string('target', 128)
                ->nullable(false)
                ->default('')
                ->comment('消息关联标示');
            $table->string('target_type', 32)
                ->nullable(false)
                ->default('')
                ->comment('消息关联标示类型');
            $table->string('sender', 128)
                ->nullable(false)
                ->default('')
                ->comment('消息发送者标示');
            $table->string('sender_type', 32)
                ->nullable(false)
                ->default('')
                ->comment('消息发送者标示类型');
            $table->string('receiver', 128)
                ->nullable(false)
                ->default('')
                ->comment('消息接收者标示');
            $table->string('receiver_type', 32)
                ->nullable(false)
                ->default('')
                ->comment('消息接收者标示类型');
            $table->timestamp('read_time')
                ->nullable()
                ->comment('消息阅读状态 null未读、!null阅读时间');
            $table->tinyInteger('process_status')
                ->nullable(false)
                ->default(0)
                ->comment('消息处理状态 0 未处理、1已处理、2处理失败');
            $table->timestamp('process_time')
                ->nullable()
                ->comment('消息最后一次处理时间');
            $table->tinyInteger('process_count')
                ->nullable(false)
                ->default(0)
                ->comment('消息处理次数');
            $table->string('class_name', 128)
                ->nullable(false)
                ->default('')
                ->comment('实例化类名称');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message');
    }
}
