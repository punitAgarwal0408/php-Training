<?php
namespace App\Controller;

use App\Controller\AppController;

class RegistrationsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
        $this->loadComponent('Flash');
    }

    public function index()
    {
        $this->paginate = ['limit' => 20, 'order' => ['registered_at' => 'DESC']];
        $query = $this->Registrations->find('all')->contain(['Users', 'TrainingSessions']);
        $registrations = $this->paginate($query);
        $this->set(compact('registrations'));
    }

    public function add()
    {
        $registration = $this->Registrations->newEntity();
        if ($this->request->is('post')) {
            $registration = $this->Registrations->patchEntity($registration, $this->request->getData());
            if ($this->Registrations->save($registration)) {
                $this->Flash->success(__('Registration successful.'));
                return $this->redirect(['controller' => 'TrainingSessions', 'action' => 'view', $registration->training_session_id]);
            }
            $this->Flash->error(__('Unable to register. Please correct the errors.'));
        }

        $trainingSessions = $this->Registrations->TrainingSessions->find('list');
        $users = $this->Registrations->Users->find('list');
        $this->set(compact('registration', 'trainingSessions', 'users'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $registration = $this->Registrations->get($id);
        $sessionId = $registration->training_session_id;
        if ($this->Registrations->delete($registration)) {
            $this->Flash->success(__('Registration cancelled.'));
        } else {
            $this->Flash->error(__('Unable to cancel registration.'));
        }

        return $this->redirect(['controller' => 'TrainingSessions', 'action' => 'view', $sessionId]);
    }
}
