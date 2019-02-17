<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $dates = ['date'];

    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class);
    }
}
