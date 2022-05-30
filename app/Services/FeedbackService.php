<?php
/**
 * Created by cynic-img.
 * User: ogenes
 * Date: 2022/2/8
 */

namespace App\Services;


use App\Models\Feedback;
use App\Services\BaseService;

class FeedbackService extends BaseService
{
    public const 
        TYPE_DEMAND = 1,
        TYPE_BUG = 2,
        TYPE_ADVISE = 3;
    
    
    public const TYPE_MAP = [
        self::TYPE_DEMAND => '新需求',
        self::TYPE_BUG => '系统BUG',
        self::TYPE_ADVISE => '优化建议',
    ];
    
    public function save(string $content, int $type):bool
    {
        Feedback::insert([
            'uid' => $this->uid,
            'content' => $content,
            'type' => $type,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        return true;
    }
}
