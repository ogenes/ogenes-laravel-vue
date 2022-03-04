<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserHasDepartment
 *
 * @property int $id 主键
 * @property int $uid 用户ID
 * @property int $dept_id 部门ID
 * @property string $created_at 创建时间
 * @method static \Illuminate\Database\Eloquent\Builder|UserHasDepartment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserHasDepartment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserHasDepartment query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserHasDepartment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserHasDepartment whereDeptId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserHasDepartment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserHasDepartment whereUid($value)
 * @mixin \Eloquent
 */
class UserHasDepartment extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'user_has_department';
}
