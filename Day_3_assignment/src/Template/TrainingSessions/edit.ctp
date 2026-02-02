<div class="container mt-4">
    <h1>Edit Training Session</h1>

    <?= $this->Form->create($trainingSession, ['class' => 'form-horizontal']) ?>

    <fieldset>
        <?= $this->Form->control('title', ['class' => 'form-control']) ?>
        <?= $this->Form->control('description', ['type' => 'textarea', 'class' => 'form-control']) ?>
        <?= $this->Form->control('instructor_id', ['options' => $users, 'class' => 'form-control']) ?>
        <?= $this->Form->control('start_date', ['class' => 'form-control', 'type' => 'datetime']) ?>
        <?= $this->Form->control('end_date', ['class' => 'form-control', 'type' => 'datetime']) ?>
        <?= $this->Form->control('max_participants', ['class' => 'form-control']) ?>
        <?= $this->Form->control('status', ['type' => 'select', 'options' => ['scheduled' => 'Scheduled', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'cancelled' => 'Cancelled'], 'class' => 'form-control']) ?>
    </fieldset>

    <?= $this->Form->button(__('Save'), ['class' => 'btn btn-primary mt-3']) ?>
    <?= $this->Html->link('Cancel', ['action' => 'index'], ['class' => 'btn btn-secondary mt-3']) ?>

    <?= $this->Form->end() ?>
</div>