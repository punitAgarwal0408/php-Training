<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\RulesChecker;
use Cake\ORM\Rule\IsUnique;
use Cake\Datasource\EntityInterface;
use ArrayObject;

class RegistrationsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('registrations');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('TrainingSessions');
        $this->belongsTo('Users');

        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('training_session_id')
            ->requirePresence('training_session_id', 'create')
            ->notEmptyString('training_session_id')
            ->integer('user_id')
            ->requirePresence('user_id', 'create')
            ->notEmptyString('user_id');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['training_session_id'], 'TrainingSessions'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->isUnique(['training_session_id', 'user_id'], 'You are already registered for this session'));

        // Rule to ensure the session is not full (non-throwing lookup)
        $rules->add(function (EntityInterface $entity, $options) {
            // Use a safe find to avoid RecordNotFoundException if the session is missing
            $session = $this->TrainingSessions->find()
                ->where(['id' => $entity->training_session_id])
                ->contain(['Registrations'])
                ->first();

            // If the session doesn't exist, fail the rule (existsIn will also handle this), don't throw
            if (!$session) {
                return false;
            }

            $registeredCount = $this->find()->where(['training_session_id' => $entity->training_session_id])->count();
            $max = $session->max_participants !== null ? (int)$session->max_participants : null;
            if ($max === null) {
                return true;
            }
            return $registeredCount < $max;
        }, 'notFull', ['errorField' => 'training_session_id', 'message' => 'This training session is full.']);

        return $rules;
    }
}
