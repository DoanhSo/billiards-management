<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TableEquipment extends Model
{
    protected $fillable = [
        'billiard_table_id',
        'name',
        'quantity',
        'broken_quantity',
        'note',
    ];

    /**
     * Phụ kiện thuộc về một bàn.
     */
    public function billiardTable(): BelongsTo
    {
        return $this->belongsTo(BilliardTable::class);
    }
}
