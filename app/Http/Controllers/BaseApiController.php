<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class BaseApiController extends BaseController
{
    /**
     * @param int $statusCodeBaseController
     * @param string|null $errorMessage
     * @param mixed|null $data
     * @return JsonResponse
     */
    public function buildResult(int $statusCode, ?string $errorMessage = null, mixed $data = null): JsonResponse
    {
        return new JsonResponse([
            'status_code' => $statusCode,
            'error_message' => $errorMessage,
            'data' => $data,
        ]);
    }
}
