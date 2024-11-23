<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class JsonException extends HttpException
{
    public function __construct(protected $data)
    {
        $this->data = $data;
        parent::__construct($this->data['metadata']['statusCode'], $this->data['metadata']['message']);
    }

    public function render()
    {
        return response()->json($this->data, intval($this->data['metadata']['statusCode']));
    }
}
