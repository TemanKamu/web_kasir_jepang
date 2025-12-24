<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// app/Models/ProofTransferPayment.php
class ProofTransferPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'image',
        'image_url'
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
