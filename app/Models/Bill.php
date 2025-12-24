<?php

namespace App\Models;

use App\Model\OrderedMenu;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

// app/Models/Bill.php
class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'queue_number',
        'user_id',
        'amount_paid',
        'change',
        'payment_method',
        'service_type',
        'status',
        'date'
    ];

    protected $casts = [
        'date' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderedMenus()
    {
        return $this->hasMany(OrderedMenu::class);
    }

    public function proofTransferPayment()
    {
        return $this->hasOne(ProofTransferPayment::class);
    }
}
