<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function getDebitAmountAttribute()
    {
        if ($this->account->isDebit()) {
            if ($this->amount >= 0.0) {
                return $this->amount;
            }
        } else if ($this->account->isCredit()) {
            if ($this->amount < 0.0) {
                return -1.0 * $this->amount;
            }
        }

        return null;
    }

    public function getCreditAmountAttribute()
    {
        if ($this->account->isCredit()) {
            if ($this->amount >= 0.0) {
                return $this->amount;
            }
        } else if ($this->account->isDebit()) {
            if ($this->amount < 0.0) {
                return -1.0 * $this->amount;
            }
        }

        return null;
    }
}
