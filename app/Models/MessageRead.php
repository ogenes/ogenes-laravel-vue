<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MessageRead
 *
 * @property int $id 主键
 * @property int $mid 消息id
 * @property int $uid 用户ID
 * @property int $times 浏览次数
 * @property \Illuminate\Support\Carbon $created_at 首次浏览时间
 * @property \Illuminate\Support\Carbon $updated_at 最近一次浏览时间
 * @method static \Illuminate\Database\Eloquent\Builder|MessageRead newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MessageRead newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MessageRead query()
 * @method static \Illuminate\Database\Eloquent\Builder|MessageRead whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageRead whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageRead whereMid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageRead whereTimes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageRead whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageRead whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MessageRead extends Model
{
    use HasFactory;
    protected $table='message_read';
}
