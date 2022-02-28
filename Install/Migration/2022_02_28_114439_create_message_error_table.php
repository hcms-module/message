<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateMessageErrorTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('message_error', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('message_id')
                ->nullable(false)
                ->default(0)
                ->comment('关联消息');
            $table->string('process_class', 256)
                ->nullable(false)
                ->default('')
                ->comment('执行处理对象');
            $table->string('error_msg', 128)
                ->nullable(false)
                ->default('')
                ->comment('错误消息');
            $table->text('error_detail')
                ->comment('错误详情');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_error');
    }
}
