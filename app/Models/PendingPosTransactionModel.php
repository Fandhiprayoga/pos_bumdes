<?php

namespace App\Models;

use CodeIgniter\Model;

class PendingPosTransactionModel extends Model
{
    protected $table            = 'pending_pos_transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'shift_id',
        'invoice_no',
        'customer_name',
        'payment_method',
        'discount_amount',
        'amount_paid',
        'subtotal_amount',
        'grand_total',
        'item_count',
        'cart_payload',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
