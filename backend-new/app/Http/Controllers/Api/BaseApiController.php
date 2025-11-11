<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BaseApiController extends Controller
{
    /**
     * Send a success response
     *
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function success($data = null, string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        // Use JSON encoding with maximum performance options
        return response()->json($response, $statusCode, [
            'Content-Type' => 'application/json',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'X-XSS-Protection' => '1; mode=block',
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
    }

    /**
     * Send an error response
     *
     * @param string $message
     * @param int $statusCode
     * @param mixed $data
     * @return JsonResponse
     */
    protected function error(string $message = 'Error', int $statusCode = 400, $data = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        // Use JSON encoding with maximum performance options
        return response()->json($response, $statusCode, [
            'Content-Type' => 'application/json',
            'X-Content-Type-Options' => 'nosniff',
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
    }

    /**
     * Send a not found response
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function notFound(string $message = 'Not Found'): JsonResponse
    {
        return $this->error($message, 404);
    }

    /**
     * Optimize database queries by selecting only needed fields
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $fields
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function optimizeQuery($query, array $fields = ['*'])
    {
        if ($fields !== ['*']) {
            return $query->select($fields);
        }
        return $query;
    }

    /**
     * Apply performance optimizations to API responses
     *
     * @param Request $request
     * @param mixed $data
     * @return mixed
     */
    protected function optimizeData(Request $request, $data)
    {
        // If data is paginated, optimize pagination
        if (is_object($data) && method_exists($data, 'toArray')) {
            $arrayData = $data->toArray();

            // Remove unnecessary pagination metadata for better performance
            if (isset($arrayData['links'])) {
                unset($arrayData['links']);
            }

            if (isset($arrayData['meta']) && is_array($arrayData['meta'])) {
                // Keep only essential metadata
                $essentialMeta = [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ];

                $arrayData['meta'] = array_intersect_key($arrayData['meta'], array_flip($essentialMeta));
            }

            return $arrayData;
        }

        return $data;
    }
}
