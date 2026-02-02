<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\FrozenTime;

class TrainingSessionsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
        $this->loadComponent('Flash');
    }

    public function index()
    {
        $this->paginate = [
            'limit' => 10,
            'order' => ['start_date' => 'ASC']
        ];

        $query = $this->TrainingSessions->find('all')->contain(['Users']);

        // Allow optional filtering by instructor
        if ($this->request->getQuery('instructor_id')) {
            $query = $this->TrainingSessions->find('byInstructor', ['instructor_id' => $this->request->getQuery('instructor_id')])->contain(['Users']);
        }

        $trainingSessions = $this->paginate($query);
        $this->set(compact('trainingSessions'));
    }

    public function view($id = null)
    {
        $trainingSession = $this->TrainingSessions->get($id, [
            'contain' => ['Users', 'Registrations.Users']
        ]);

        $users = $this->TrainingSessions->Users->find('list');
        $this->set(compact('trainingSession', 'users'));
    }

    public function add()
    {
        $trainingSession = $this->TrainingSessions->newEntity();
        if ($this->request->is('post')) {
            $trainingSession = $this->TrainingSessions->patchEntity($trainingSession, $this->request->getData());
            if ($this->TrainingSessions->save($trainingSession)) {
                $this->Flash->success(__('Training session has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to add training session. Please correct the errors.'));
        }

        $users = $this->TrainingSessions->Users->find('list');
        $this->set(compact('trainingSession', 'users'));
    }

    public function edit($id = null)
    {
        $trainingSession = $this->TrainingSessions->get($id);
        if ($this->request->is(['post', 'put', 'patch'])) {
            $trainingSession = $this->TrainingSessions->patchEntity($trainingSession, $this->request->getData());
            if ($this->TrainingSessions->save($trainingSession)) {
                $this->Flash->success(__('Training session has been updated.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to update training session. Please correct the errors.'));
        }

        $users = $this->TrainingSessions->Users->find('list');
        $this->set(compact('trainingSession', 'users'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $trainingSession = $this->TrainingSessions->get($id);
        if ($this->TrainingSessions->delete($trainingSession)) {
            $this->Flash->success(__('Training session deleted.'));
        } else {
            $this->Flash->error(__('Unable to delete training session.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    // Custom action to list upcoming sessions
    public function upcoming()
    {
        $query = $this->TrainingSessions->find('upcoming')->contain(['Users']);
        $trainingSessions = $this->paginate($query);
        $this->set(compact('trainingSessions'));
    }
}
