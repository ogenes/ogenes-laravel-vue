<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Dict
 *
 * @property int $id 主键
 * @property string $dict_name 字典名称
 * @property string $symbol 字典符号
 * @property string $remark 备注
 * @property \Illuminate\Support\Carbon $created_at 创建时间
 * @property \Illuminate\Support\Carbon $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder|Dict newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Dict newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Dict query()
 * @method static \Illuminate\Database\Eloquent\Builder|Dict whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dict whereDictName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dict whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dict whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dict whereSymbol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dict whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Dict extends Model
{
    use HasFactory;
    protected $table = 'dict';
}
