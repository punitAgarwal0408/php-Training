<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Registration extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];
}
