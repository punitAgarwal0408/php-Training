<div class="container mt-4">
    <h1>Registrations</h1>

    <table class="table table-striped">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('id') ?></th>
                <th><?= $this->Paginator->sort('training_session_id', 'Session') ?></th>
                <th><?= $this->Paginator->sort('user_id', 'User') ?></th>
                <th><?= $this->Paginator->sort('registered_at') ?></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($registrations as $reg): ?>
            <tr>
                <td><?= h($reg->id) ?></td>
                <td><?= $reg->has('training_session') ? h($reg->training_session->title) : h($reg->training_session_id) ?></td>
                <td><?= $reg->has('user') ? h($reg->user->name) : h($reg->user_id) ?></td>
                <td><?= h($reg->registered_at) ?></td>
                <td>
                    <?= $this->Form->postLink('Delete', ['action' => 'delete', $reg->id], ['confirm' => 'Cancel registration?', 'class' => 'btn btn-sm btn-danger']) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        <?= $this->element('pagination'); ?>
    </div>
</div>