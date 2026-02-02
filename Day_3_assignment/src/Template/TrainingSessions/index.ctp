<div class="container mt-4">
    <h1>Training Sessions</h1>

    <div class="mb-3">
        <?= $this->Html->link('Add Training Session', ['action' => 'add'], ['class' => 'btn btn-primary']) ?>
        <?= $this->Html->link('Upcoming Sessions', ['action' => 'upcoming'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('title') ?></th>
                <th><?= $this->Paginator->sort('instructor_id', 'Instructor') ?></th>
                <th><?= $this->Paginator->sort('start_date') ?></th>
                <th><?= $this->Paginator->sort('end_date') ?></th>
                <th><?= $this->Paginator->sort('status') ?></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($trainingSessions as $session): ?>
            <tr>
                <td><?= h($session->title) ?></td>
                <td><?= $session->has('user') ? h($session->user->name) : h($session->instructor_id) ?></td>
                <td><?= h($session->start_date) ?></td>
                <td><?= h($session->end_date) ?></td>
                <td><?= h($session->status) ?></td>
                <td>
                    <?= $this->Html->link('View', ['action' => 'view', $session->id], ['class' => 'btn btn-sm btn-info']) ?>
                    <?= $this->Html->link('Edit', ['action' => 'edit', $session->id], ['class' => 'btn btn-sm btn-warning']) ?>
                    <?= $this->Form->postLink('Delete', ['action' => 'delete', $session->id], ['confirm' => 'Are you sure?', 'class' => 'btn btn-sm btn-danger']) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        <?= $this->element('pagination'); ?>
    </div>
</div>