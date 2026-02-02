<div class="container mt-4">
    <h1>Register for a Session</h1>

    <?= $this->Form->create($registration) ?>

    <fieldset>
        <?= $this->Form->control('training_session_id', ['options' => $trainingSessions, 'class' => 'form-control']) ?>
        <?= $this->Form->control('user_id', ['options' => $users, 'class' => 'form-control']) ?>
    </fieldset>

    <?= $this->Form->button(__('Register'), ['class' => 'btn btn-success mt-3']) ?>
    <?= $this->Html->link('Back', ['controller' => 'TrainingSessions', 'action' => 'index'], ['class' => 'btn btn-secondary mt-3']) ?>

    <?= $this->Form->end() ?>
</div>