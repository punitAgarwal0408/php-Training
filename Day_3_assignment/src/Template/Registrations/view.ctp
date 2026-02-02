<div class="container mt-4">
    <h1>Registration #<?= h($registration->id) ?></h1>

    <p><strong>Session:</strong> <?= $registration->has('training_session') ? h($registration->training_session->title) : h($registration->training_session_id) ?></p>
    <p><strong>User:</strong> <?= $registration->has('user') ? h($registration->user->name) : h($registration->user_id) ?></p>
    <p><strong>Registered at:</strong> <?= h($registration->registered_at) ?></p>

    <div class="mt-3">
        <?= $this->Html->link('Back to list', ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
    </div>
</div>