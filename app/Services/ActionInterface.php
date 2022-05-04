<?php

namespace App\Services;

interface ActionInterface
{
    function handle(&$command);
}