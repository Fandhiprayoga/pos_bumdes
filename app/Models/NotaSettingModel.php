<?php

namespace App\Models;

use CodeIgniter\Model;

class NotaSettingModel extends Model
{
    protected $table            = 'nota_settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'paper_size',
        'custom_width',
        'font_size',
        'font_family',
        'header_text',
        'header_icon',
        'footer_text',
        'show_logo',
        'logo_size',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get current nota settings or default
     */
    public function getSettings(): array
    {
        $settings = $this->first();
        
        if (! $settings) {
            return $this->getDefaults();
        }

        return $settings;
    }

    /**
     * Get default nota settings
     */
    public function getDefaults(): array
    {
        return [
            'id'           => 0,
            'paper_size'   => '80mm',
            'custom_width' => null,
            'font_size'    => 12,
            'font_family'  => 'Courier New',
            'header_text'  => 'Nota Penjualan',
            'header_icon'  => null,
            'footer_text'  => 'Terima kasih telah berbelanja',
            'show_logo'    => 1,
            'logo_size'    => 'medium',
        ];
    }
}
