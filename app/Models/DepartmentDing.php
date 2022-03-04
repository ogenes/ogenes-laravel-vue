<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DepartmentDing
 *
 * @property int $id 主键
 * @property string $name 部门名字
 * @property int $parent_id 上级部门ID
 * @property int $ding_dept_id 钉钉部门ID
 * @property int $ding_parent_id 钉钉上级部门ID
 * @property \Illuminate\Support\Carbon $created_at 创建时间
 * @property \Illuminate\Support\Carbon $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder|DepartmentDing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DepartmentDing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DepartmentDing query()
 * @method static \Illuminate\Database\Eloquent\Builder|DepartmentDing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DepartmentDing whereDingDeptId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DepartmentDing whereDingParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DepartmentDing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DepartmentDing whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DepartmentDing whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DepartmentDing whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DepartmentDing extends Model
{
    use HasFactory;
    protected $table = 'department_ding';
}
