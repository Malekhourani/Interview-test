<?php

namespace App\Services;

interface ActionFactoryInterface
{
    function factory($action): ActionInterface;
}
