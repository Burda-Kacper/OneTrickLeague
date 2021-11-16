<?php

namespace App\DataSctructure;

class MainResponse
{
    private $success;
    private $data;

    public function __construct(bool $success, mixed $data = null)
    {
        $this->success = $success;
        $this->data = $data;
    }
    public function toJsonResponse(): array
    {
        return [
            'success' => $this->success,
            'data' => $this->data
        ];
    }
    public function getData(): mixed
    {
        return $this->data;
    }
}
