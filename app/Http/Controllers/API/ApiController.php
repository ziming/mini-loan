<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response as IlluminateResponse;

class ApiController extends Controller
{
    protected $statusCode = 200;

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondNotFound($message = 'Not Found!')
    {
        return $this->setStatusCode(IlluminateResponse::HTTP_NOT_FOUND)->respondWithError($message);
    }

    /**
     * @param $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithError($message)
    {
        return $this->respond([
            'error' => [
                'message'     => $message,
                'status_code' => $this->getStatusCode()
            ]
        ]);
    }

    /**
     * @param $data
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respond($data, $headers = [])
    {
        return response()->json($data, $this->getStatusCode(), $headers);
    }

    protected function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param $statusCode
     * @return $this
     */
    protected function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondUnauthorized($message = 'Unauthorized!')
    {
        return $this->setStatusCode(IlluminateResponse::HTTP_UNAUTHORIZED)->respondWithError($message);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondInternalError($message = 'Internal Error!')
    {
        return $this->setStatusCode(IlluminateResponse::HTTP_INTERNAL_SERVER_ERROR)->respondWithError($message);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondConflictError($message = 'Conflict!')
    {
        return $this->setStatusCode(IlluminateResponse::HTTP_CONFLICT)->respondWithError($message);
    }

    /**
     * @param $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondCreated($message)
    {
        return $this->setStatusCode(IlluminateResponse::HTTP_CREATED)->respond([
            'message' => $message,
        ]);
    }
}
