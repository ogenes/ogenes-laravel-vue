<?php
/**
 * Created by cynic-img.
 * User: ogenes
 * Date: 2022/2/8
 */

namespace App\Services;


use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;

class LanguageService extends BaseService
{
    protected const LANG_KEY = 'OG:LANG:';
    
    public function getLang(): string
    {
        $key = $this->getLangKey();
        
        $cache = $this->getRedis()->get($key);
        if ($cache) {
            return $cache;
        }
        return config('app.locale');
    }
    
    public function setLang(string $lang): bool
    {
        $new = $lang === 'cn' ? 'zh' : 'en';
        $key = $this->getLangKey();
        $this->getRedis()->set($key, $new);
        App::setLocale($new);
        return true;
    }
    
    protected function getLangKey(): string
    {
        return $this->uid > 0 ? self::LANG_KEY . 'USER:' . $this->uid : '';
    }
    
}
