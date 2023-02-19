<?php

namespace App\Controller\Client;

use App\Controller\Authenticate\UserAuthController;
use App\Controller\User\UserController;
use App\Model\Client;

require __DIR__ . '/../../../vendor/autoload.php';

class ClientController extends UserController
{
    private Client $client;

    public function __construct()
    {
        try {
            parent::__construct();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function defaultAction(Object|array|string|int $optional = null)
    {
    }

    public function auth(): bool
    {
        return parent::auth();
    }


    public function sendFeedback() {}
}
