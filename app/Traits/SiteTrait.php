<?php

namespace App\Traits;

use App\Exceptions\JsonException;

trait SiteTrait
{
    protected function sendResponse($statusCode, $message, $data = null)
    {
        $response = [
            'metadata' => [
                'statusCode' => $statusCode,
                'status' => 'success',
                'message' => $message,
            ],
        ];

        if ($data) {
            $response['data'] = $data;
        }

        if ($statusCode != 200) {
            $response['metadata']['status'] = 'error';

            throw new JsonException($response);
        }

        return response()->json($response, $statusCode);
    }

    protected function storeFile($file, $path, $name = null)
    {
        $name = $name ?: str_replace(['public', '/'], '', $path);
        $filename = "$name-".rand().'-'.time().".{$file->getClientOriginalExtension()}";

        $file->storeAs($path, $filename);

        return "$name/$filename";
    }
}
