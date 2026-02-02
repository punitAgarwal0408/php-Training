<?php
namespace App\Controller;

use App\Controller\AppController;

class PagesController extends AppController
{
    public function index()
    {
        // Redirect root to training sessions index
        return $this->redirect(['controller' => 'TrainingSessions', 'action' => 'index']);
    }
}
