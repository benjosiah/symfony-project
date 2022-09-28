<?php

namespace App\MessageHandler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Message\PerseMessage;
use App\Service\PersingService;

#[AsMessageHandler]
class PerseMessegeHandler
{
    protected $persingService;
    public function __construct(PersingService $persingService)
    {
        $this->persingService = $persingService;
    }

    public function __invoke(PerseMessage $message)
    {
        $this->persingService->getArray($message->getUrl(), true);
    }
}
