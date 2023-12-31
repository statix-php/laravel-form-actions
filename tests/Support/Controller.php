<?php

namespace Statix\FormAction\Tests\Support;

class Controller
{
    public function store(CreateTeamAction $action)
    {
        return $action
            ->authorize()
            ->validate()
            ->handle();
    }
}
