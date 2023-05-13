<?php

namespace Tests\Core;

use Core\Token;
use Tests\Tester;

require __DIR__ . '/../../vendor/autoload.php';

class TestToken extends Tester
{

    private Token $token;

    public function __construct()
    {
        parent::__construct(Token::class);
    }

    public function setup(): void
    {
        $this->token = new Token();
    }

    public function run(): void
    {
        $this->setup();
        $this->summary();
    }
}