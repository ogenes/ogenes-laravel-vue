<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\MenuTrans
 *
 * @property int $id 主键
 * @property int $menu_id 菜单ID
 * @property string $language 语言
 * @property string $title 菜单
 * @property \Illuminate\Support\Carbon $created_at 创建时间
 * @property \Illuminate\Support\Carbon $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder|MenuTrans newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MenuTrans newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MenuTrans query()
 * @method static \Illuminate\Database\Eloquent\Builder|MenuTrans whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MenuTrans whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MenuTrans whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MenuTrans whereMenuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MenuTrans whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MenuTrans whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MenuTrans extends Model
{
    use HasFactory;
    protected $table = 'menu_trans';
}
