<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RoleHasMenu
 *
 * @property int $id 主键
 * @property int $role_id 角色ID
 * @property int $menu_id 菜单ID
 * @property \Illuminate\Support\Carbon $created_at 创建时间
 * @method static \Illuminate\Database\Eloquent\Builder|RoleHasMenu newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleHasMenu newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleHasMenu query()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleHasMenu whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleHasMenu whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleHasMenu whereMenuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleHasMenu whereRoleId($value)
 * @mixin \Eloquent
 */
class RoleHasMenu extends Model
{
    use HasFactory;
    protected $table = 'role_has_menu';
}
