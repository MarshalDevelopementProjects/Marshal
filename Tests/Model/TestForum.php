<?php

namespace Tests\Model;

require __DIR__ . '/../../vendor/autoload.php';

use App\Model\Forum;
use Tests\Tester;

class TestForum extends Tester
{

    private Forum $forum;

    public function __construct()
    {
        parent::__construct(Forum::class);
    }

    protected function setup(): void
    {
        $this->forum = new Forum();
    }

    public function run(): void
    {
        // TODO: Implement run() method.
    }
}