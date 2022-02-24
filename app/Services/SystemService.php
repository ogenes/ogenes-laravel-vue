<?php
/**
 * Created by cynic-img.
 * User: ogenes
 * Date: 2022/2/7
 */

namespace App\Services;


class SystemService extends BaseService
{
    public function init(): array
    {
        $ret['lang'] = LanguageService::getInstance()->getLang();
        return $ret;
    }
}
