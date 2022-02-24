<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SmsRecord
 *
 * @property int $id 主键
 * @property int $uid 用户ID
 * @property string $mobile 手机号
 * @property string $sign_name 签名
 * @property string $template_code 模板
 * @property string $template_param 模板参数
 * @property \Illuminate\Support\Carbon $created_at 创建时间
 * @property string $send_at 发送时间
 * @property int $result 1成功，0失败
 * @property string $reason 失败原因
 * @method static \Illuminate\Database\Eloquent\Builder|SmsRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SmsRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SmsRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|SmsRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsRecord whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsRecord whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsRecord whereResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsRecord whereSendAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsRecord whereSignName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsRecord whereTemplateCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsRecord whereTemplateParam($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsRecord whereUid($value)
 * @mixin \Eloquent
 */
class SmsRecord extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'cynic.cy_sms_record';
}
