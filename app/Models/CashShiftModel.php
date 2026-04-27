<?php

namespace App\Models;

use CodeIgniter\Model;

class CashShiftModel extends Model
{
    protected $table            = 'cash_shifts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'opened_at',
        'opening_cash',
        'closed_at',
        'closing_cash_system',
        'closing_cash_actual',
        'variance',
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
