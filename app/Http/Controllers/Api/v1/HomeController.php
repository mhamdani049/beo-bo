<?php
namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 *
 */
class HomeController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function welcome(Request $request): JsonResponse
    {
        $user = $this->currentUser();
        return response()->json($user, 200);
    }
}
