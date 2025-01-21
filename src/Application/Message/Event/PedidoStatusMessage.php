<?php

namespace App\Application\Message\Event;

class PedidoStatusMessage
{
    private int $pedidoId;
    private string $status;

    public function __construct(int $pedidoId, string $status)
    {
        $this->pedidoId = $pedidoId;
        $this->status = $status;
    }

    public function getPedidoId(): int
    {
        return $this->pedidoId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}