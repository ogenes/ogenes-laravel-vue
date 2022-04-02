<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ActionLog
 *
 * @property int $id 主键
 * @property string $resource 资源
 * @property int $resource_id 资源ID
 * @property int $uid 操作人
 * @property string $type 操作类型
 * @property string $remark 操作备注
 * @property \Illuminate\Support\Carbon $created_at 操作时间
 * @method static \Illuminate\Database\Eloquent\Builder|ActionLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ActionLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ActionLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|ActionLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActionLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActionLog whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActionLog whereResource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActionLog whereResourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActionLog whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActionLog whereUid($value)
 * @mixin \Eloquent
 */
class ActionLog extends Model
{
    use HasFactory;
    protected $table = 'action_log';
}
