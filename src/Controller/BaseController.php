<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

// exceptions
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BaseController extends Controller {
    
    protected function enforceRouteParams(string $param, array $validate) {
        if (!in_array($param, $validate)) {
            throw new NotFoundHttpException('cannot get url');
        }
    }
}