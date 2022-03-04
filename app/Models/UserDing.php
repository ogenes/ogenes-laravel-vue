<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserDing
 *
 * @property int $id 主键
 * @property int $uid 用户ID
 * @property string $userid
 * @property string $unionid
 * @property string $name 用户名
 * @property string $mobile 用户手机号
 * @property string $email
 * @property string $title
 * @property string $boss
 * @property string $hired_date 入职时间
 * @property int $active 是否激活
 * @property int $leader
 * @property string $avatar 用户头像
 * @property \Illuminate\Support\Carbon $created_at 创建时间
 * @property \Illuminate\Support\Carbon $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder|UserDing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserDing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserDing query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserDing whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDing whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDing whereBoss($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDing whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDing whereHiredDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDing whereLeader($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDing whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDing whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDing whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDing whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDing whereUnionid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDing whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDing whereUserid($value)
 * @mixin \Eloquent
 */
class UserDing extends Model
{
    use HasFactory;
    protected $fillable = [
        'uid',
        'userid',
        'unionid',
        'name',
        'mobile',
        'email',
        'title',
        'boss',
        'hired_date',
        'active',
        'leader',
        'avatar',
    ];
    protected $table = 'user_ding';
}
