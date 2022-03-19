<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserHasRole
 *
 * @property int $id 主键
 * @property int $user_id 用户ID
 * @property int $role_id 角色ID
 * @property \Illuminate\Support\Carbon $created_at 创建时间
 * @method static \Illuminate\Database\Eloquent\Builder|UserHasRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserHasRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserHasRole query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserHasRole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserHasRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserHasRole whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserHasRole whereUserId($value)
 * @mixin \Eloquent
 */
class UserHasRole extends Model
{
    use HasFactory;
    protected $table = 'user_has_role';
}
