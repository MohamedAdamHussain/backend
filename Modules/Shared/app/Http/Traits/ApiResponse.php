<?php
namespace Modules\shared\Http\Traits;


trait ApiResponse
{
    protected function successResponse($data, $code = 200)
    {
        return response()->json(['status' => 'success', 'data' => $data], $code);
    }

    protected function errorResponse($message, $code)
    {
        return response()->json(['status' => 'error', 'message' => $message], $code);
    }
}
