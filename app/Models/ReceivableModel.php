<?php

namespace App\Models;

use CodeIgniter\Model;

class ReceivableModel extends Model
{
    protected $table            = 'receivables';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'sale_id',
        'customer_id',
        'credit_term',
        'due_date',
        'grand_total',
        'amount_paid',
        'outstanding',
        'status',
        'notes',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}