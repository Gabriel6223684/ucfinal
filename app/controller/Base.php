<?php

namespace app\controller;
declare(strict_types=1);

use app\trait\Response;
use app\trait\Template;

abstract class Base
{
    use Template, Response;
}
