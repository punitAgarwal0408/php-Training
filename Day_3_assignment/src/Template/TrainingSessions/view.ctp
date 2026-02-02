<div class="container mt-4">
    <h1><?= h($trainingSession->title) ?></h1>

    <p><strong>Instructor:</strong> <?= $trainingSession->has('user') ? h($trainingSession->user->name) : h($trainingSession->instructor_id) ?></p>
    <p><strong>Start:</strong> <?= h($trainingSession->start_date) ?></p>
    <p><strong>End:</strong> <?= h($trainingSession->end_date) ?></p>
    <p><strong>Duration:</strong> <?= h($trainingSession->duration) ?></p>
    <p><strong>Max participants:</strong> <?= h($trainingSession->max_participants) ?></p>
    <p><strong>Status:</strong> <?= h($trainingSession->status) ?></p>

    <h4>Description</h4>
    <p><?= $this->Text->autoParagraph(h($trainingSession->description)) ?></p>

    <h4>Registrations</h4>
    <?php if (!empty($trainingSession->registrations)): ?>
        <ul>
        <?php foreach ($trainingSession->registrations as $reg): ?>
            <li>
                <?= $reg->has('user') ? h($reg->user->name) : h($reg->user_id) ?>
                <small class="text-muted">(<?= h($reg->registered_at) ?>)</small>
                <?= $this->Form->postLink('Cancel', ['controller' => 'Registrations', 'action' => 'delete', $reg->id], ['confirm' => 'Cancel registration?', 'class' => 'btn btn-sm btn-danger ml-2']) ?>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No registrations yet.</p>
    <?php endif; ?>

    <p><strong>Spots left:</strong> <?= h($trainingSession->max_participants - count($trainingSession->registrations)) ?></p>

    <div class="card mb-3">
        <div class="card-body">
            <?= $this->Form->create(null, ['url' => ['controller' => 'Registrations', 'action' => 'add']]) ?>
                <?= $this->Form->hidden('training_session_id', ['value' => $trainingSession->id]) ?>
                <?= $this->Form->control('user_id', ['label' => 'Your User', 'options' => $users, 'empty' => '-- Select --', 'class' => 'form-control']) ?>
                <?= $this->Form->button('Register', ['class' => 'btn btn-success mt-2']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>

    <div class="mt-3">
        <?= $this->Html->link('Edit', ['action' => 'edit', $trainingSession->id], ['class' => 'btn btn-warning']) ?>
        <?= $this->Form->postLink('Delete', ['action' => 'delete', $trainingSession->id], ['confirm' => 'Are you sure?', 'class' => 'btn btn-danger']) ?>
        <?= $this->Html->link('Back to list', ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
    </div>
</div>