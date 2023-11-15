<?php 

namespace Src\App\Middlewares;

use GTG\MVC\Middleware;

class GuestMiddleware extends Middleware 
{
    public function handle($router): bool
    {
        $user = $this->session->getAuth();
        if($user) {
            if($user->isAdmin()) {
                $this->redirect('admin.index');
            } else {
                $this->redirect('user.index');
            }
            return false;
        }

        return true;
    }
}