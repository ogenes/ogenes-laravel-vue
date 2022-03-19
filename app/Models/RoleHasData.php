<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RoleHasData
 *
 * @property int $id 主键
 * @property int $role_id 角色ID
 * @property int $data_id 数据权限ID
 * @property \Illuminate\Support\Carbon $created_at 创建时间
 * @method static \Illuminate\Database\Eloquent\Builder|RoleHasData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleHasData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleHasData query()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleHasData whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleHasData whereDataId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleHasData whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleHasData whereRoleId($value)
 * @mixin \Eloquent
 */
class RoleHasData extends Model
{
    use HasFactory;
    protected $table = 'role_has_data';
}
