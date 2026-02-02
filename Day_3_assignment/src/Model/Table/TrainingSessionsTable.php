<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\Query;
use DateTime;

class TrainingSessionsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('training_sessions');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->belongsTo('Users', [
            'foreignKey' => 'instructor_id'
        ]);

        $this->hasMany('Registrations', [
            'foreignKey' => 'training_session_id'
        ]);

        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->notEmptyString('title', 'Title is required')
            ->dateTime('start_date')
            ->dateTime('end_date')
            ->add('start_date', 'future', [
                'rule' => function ($value, $context) {
                    $start = new DateTime($value);
                    $now = new DateTime();
                    return $start > $now;
                },
                'message' => 'Start date must be in the future'
            ])
            ->add('end_date', 'greaterThanStart', [
                'rule' => function ($value, $context) {
                    if (empty($context['data']['start_date'])) {
                        return false;
                    }
                    $start = new DateTime($context['data']['start_date']);
                    $end = new DateTime($value);
                    return $end > $start;
                },
                'message' => 'End date must be after start date'
            ])
            ->integer('max_participants')
            ->allowEmptyString('max_participants')
            ->allowEmptyString('description');

        return $validator;
    }

    public function findUpcoming(Query $query, array $options)
    {
        return $query->where(['start_date >' => new DateTime()])
                     ->order(['start_date' => 'ASC']);
    }

    public function findByInstructor(Query $query, array $options)
    {
        if (empty($options['instructor_id'])) {
            return $query;
        }

        return $query->where(['TrainingSessions.instructor_id' => $options['instructor_id']]);
    }
}
