<?php

namespace Statix\FormAction\Tests\Support;

use Illuminate\Foundation\Auth\User as Authenticatable;

class TestModel extends Authenticatable
{
    protected $table = 'test_models';

    protected $guarded = [];

    public $timestamps = false;
}