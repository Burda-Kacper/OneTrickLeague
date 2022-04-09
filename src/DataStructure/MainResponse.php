<?php

namespace App\DataStructure;

class MainResponse
{
    private bool $success;
    private mixed $data;

    /**
     * @param bool $success
     * @param mixed|null $data
     */
    public function __construct(bool $success, mixed $data = null)
    {
        $this->success = $success;
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function toJsonResponse(): array
    {
        return [
            'success' => $this->success,
            'data' => $this->data
        ];
    }

    /**
     * @return bool
     */
    public function getSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @return mixed
     */
    public function getData(): mixed
    {
        return $this->data;
    }
}
