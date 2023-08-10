<?php

namespace Statix\FormAction\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Statix\FormAction\FormAction
 */
class FormAction extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Statix\FormAction\FormAction::class;
    }
}
