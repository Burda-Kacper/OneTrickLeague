<?php

namespace App\DataStructure;

class MainResponse
{
    private bool $success;
    private mixed $data;

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
    public function getSuccess(): bool
    {
        return $this->success;
    }
    public function getData(): mixed
    {
        return $this->data;
    }
}
