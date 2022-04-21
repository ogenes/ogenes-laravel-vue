<?php
/**
 * Created by cynic-img.
 * User: ogenes
 * Date: 2022/2/8
 */

namespace App\Services;


use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardService extends BaseService
{
    public function userGroup():array 
    {
        $ret = User::select([
                DB::raw('count( 1 ) AS allUser'),
                DB::raw('sum( IF ( DATE_SUB( CURDATE( ), INTERVAL 7 DAY ) <= last_login_at, 1, 0 ) ) AS activeUser'),
                DB::raw('sum( IF ( DATE_SUB( CURDATE( ), INTERVAL 5 MINUTE ) <= last_login_at, 1, 0 ) ) AS onlineUser '),
            ])
            ->first();
        return $ret ? $ret->toArray() : [];
    }
}
