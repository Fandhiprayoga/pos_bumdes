<?php

namespace App\Models;

use CodeIgniter\Model;

class ReceivablePaymentModel extends Model
{
    protected $table            = 'receivable_payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'receivable_id',
        'payment_date',
        'amount',
        'payment_method',
        'reference_no',
        'status',
        'notes',
        'created_by',
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