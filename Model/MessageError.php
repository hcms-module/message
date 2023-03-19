<?php

declare (strict_types=1);

namespace App\Application\Message\Model;

use Hyperf\DbConnection\Model\Model;

/**
 */
class MessageError extends Model
{
    /**
     * The table associated with the model.
     *
     * @var ?string
     */
    protected ?string $table = 'message_error';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $fillable = ['message_id', 'process_class', 'error_msg', 'error_detail'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected array $casts = [];
}