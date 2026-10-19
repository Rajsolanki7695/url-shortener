<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ShortUrl extends Model
{
    protected $table = 'short_urls';

    protected $fillable = [
        'original_url',
        'short_code',
        'company_id',
        'created_by'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeApplyVisibilityConstraints(Builder $query, $user)
    {
        if ((int) $user->role_id === 1) {
            // SuperAdmin can see the list of all short urls for every company
            return $query;
        } elseif ((int) $user->role_id === 2) {
            // Admin can only see the list of all short urls created in their own company
            $query->where('company_id', $user->company_id);
        } elseif ((int) $user->role_id === 3) {
            // Member can only see the list of all short urls created by themselves
            $query->where('created_by', $user->id);
        }
    }
}
