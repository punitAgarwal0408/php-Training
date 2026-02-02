<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\I18n\FrozenTime;

class TrainingSession extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];

    protected $_virtual = ['duration', 'is_full'];

    protected function _getDuration()
    {
        if (empty($this->start_date) || empty($this->end_date)) {
            return null;
        }

        $start = new FrozenTime($this->start_date);
        $end = new FrozenTime($this->end_date);
        $diff = $end->diff($start);

        $parts = [];
        if ($diff->d) {
            $parts[] = $diff->d . ' day' . ($diff->d > 1 ? 's' : '');
        }
        if ($diff->h) {
            $parts[] = $diff->h . ' hour' . ($diff->h > 1 ? 's' : '');
        }
        if (empty($parts)) {
            $parts[] = $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
        }

        return implode(' ', $parts);
    }

    protected function _getIsFull()
    {
        // If registrations are loaded, use them; otherwise we cannot know and default to false
        if (isset($this->registrations) && is_array($this->registrations)) {
            $count = count($this->registrations);
            return ($this->max_participants !== null) && ($count >= $this->max_participants);
        }

        return false;
    }
}
