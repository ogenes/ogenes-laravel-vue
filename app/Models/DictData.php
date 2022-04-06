<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DictData
 *
 * @property int $id 主键
 * @property int $dict_id 字典ID
 * @property int $sort 排序
 * @property string $label 标签
 * @property string $value 值
 * @property string $remark 备注
 * @property int $disable 为1不可编辑和删除
 * @property \Illuminate\Support\Carbon $created_at 创建时间
 * @property \Illuminate\Support\Carbon $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder|DictData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DictData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DictData query()
 * @method static \Illuminate\Database\Eloquent\Builder|DictData whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData whereDictId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData whereDisable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DictData whereValue($value)
 * @mixin \Eloquent
 */
class DictData extends Model
{
    use HasFactory;
    protected $table = 'dict_data';
}
