<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Message
 *
 * @property int $id 主键
 * @property int $author_id 用户ID
 * @property int $cat_id 类别ID
 * @property string $title 标题
 * @property string $banner 图片
 * @property string $desc 简述
 * @property string $text 内容
 * @property int $top 是否置顶：1是，0不隐藏
 * @property int $hidden 是否隐藏：1是，0不隐藏
 * @property string $publisher 公布者
 * @property string $publish_time 公布时间
 * @property \Illuminate\Support\Carbon $created_at 创建时间
 * @property \Illuminate\Support\Carbon $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder|Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Message query()
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereBanner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereCatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereHidden($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message wherePublishTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message wherePublisher($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereTop($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Message extends Model
{
    use HasFactory;
    protected $table='message';
}
