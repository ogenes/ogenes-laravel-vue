<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DataPermission
 *
 * @property int $id 主键
 * @property int $system_id 系统ID
 * @property int $menu_id 菜单ID
 * @property string $resource 数据源
 * @property string $data_mark 数据权限标识
 * @property string $data_name 数据权限名称
 * @property string $conditions 条件参数
 * @property string $fields 结果字段
 * @property \Illuminate\Support\Carbon $created_at 创建时间
 * @property \Illuminate\Support\Carbon $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder|DataPermission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DataPermission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DataPermission query()
 * @method static \Illuminate\Database\Eloquent\Builder|DataPermission whereConditions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataPermission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataPermission whereDataMark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataPermission whereDataName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataPermission whereFields($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataPermission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataPermission whereMenuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataPermission whereResource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataPermission whereSystemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataPermission whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DataPermission extends Model
{
    use HasFactory;
    protected $table = 'data_permission';
}
