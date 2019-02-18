<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    const TYPE_ASSET = 1;
    const TYPE_EXPENSE = 2;
    const TYPE_EQUITY = 3;
    const TYPE_INCOME = 4;
    const TYPE_LIABILITY = 5;

    public static function getTypes()
    {
        return [
            self::TYPE_ASSET => self::getTypeName(self::TYPE_ASSET),
            self::TYPE_EQUITY => self::getTypeName(self::TYPE_EQUITY),
            self::TYPE_EXPENSE => self::getTypeName(self::TYPE_EXPENSE),
            self::TYPE_INCOME => self::getTypeName(self::TYPE_INCOME),
            self::TYPE_LIABILITY => self::getTypeName(self::TYPE_LIABILITY)
        ];
    }

    public static function getTypeName(int $type)
    {
        switch ($type) {
            case self::TYPE_ASSET:
                return __('Asset');

            case self::TYPE_EXPENSE:
                return __('Expense');

            case self::TYPE_EQUITY:
                return __('Equity');

            case self::TYPE_INCOME:
                return __('Income');

            case self::TYPE_LIABILITY:
                return __('Liability');

            default:
                return __('Unknown');
        }
    }

    public function getBalanceAttribute($value)
    {
        return round($value, 2);
    }

    public function getTypeNameAttribute()
    {
        return self::getTypeName($this->type);
    }

    public function isDebit()
    {
        switch ($this->type) {
            case self::TYPE_ASSET:
                return true;

            case self::TYPE_EXPENSE:
                return true;

            case self::TYPE_EQUITY:
                return false;

            case self::TYPE_INCOME:
                return false;

            case self::TYPE_LIABILITY:
                return false;

            default:
                return true;
        }
    }

    public function isCredit()
    {
        return !$this->isDebit();
    }
}
