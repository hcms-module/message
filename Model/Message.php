<?php

declare (strict_types=1);

namespace App\Application\Message\Model;

use Hyperf\Database\Model\Relations\HasOne;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int            $message_id
 * @property string         $title
 * @property string         $content
 * @property string         $target
 * @property string         $target_type
 * @property string         $sender
 * @property string         $sender_type
 * @property string         $receiver
 * @property string         $receiver_type
 * @property int            $read_status
 * @property int            $process_status
 * @property string         $process_time
 * @property int            $process_count
 * @property string         $class_name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Message extends Model
{

    protected $primaryKey = 'message_id';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'message';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'message_id' => 'integer',
        'read_status' => 'integer',
        'process_status' => 'integer',
        'process_count' => 'integer',
        'process_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    const PROCESS_STATUS_SUCCESS = 1;
    const PROCESS_STATUS_FAILED = 2;
    const PROCESS_STATUS_PENDING = 0;

    public function lastErrorInfo(): HasOne
    {
        return $this->hasOne(MessageError::class, 'message_id', 'message_id')
            ->orderBy('id', 'DESC');
    }
}