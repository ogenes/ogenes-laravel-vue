<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FileUpload
 *
 * @property int $id 主键
 * @property int $uid 用户ID
 * @property string $source 上传来源
 * @property string $original_name 文件名
 * @property string $object_id 唯一标识
 * @property string $path 路径
 * @property int $size size
 * @property string $ext 扩展名
 * @property int $file_status 1正常，0已删除
 * @property \Illuminate\Support\Carbon $created_at 创建时间
 * @method static \Illuminate\Database\Eloquent\Builder|FileUpload newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FileUpload newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FileUpload query()
 * @method static \Illuminate\Database\Eloquent\Builder|FileUpload whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileUpload whereExt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileUpload whereFileStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileUpload whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileUpload whereObjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileUpload whereOriginalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileUpload wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileUpload whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileUpload whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileUpload whereUid($value)
 * @mixin \Eloquent
 */
class FileUpload extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'file_upload';
}
