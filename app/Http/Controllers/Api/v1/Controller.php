<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 *
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @return array
     */
    public function currentUser(): array
    {
        $token = JWTAuth::getToken();
        return JWTAuth::getPayload($token)->toArray();
    }
}