<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opd extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'tm_opd';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'n_opd',
        'alamat',
        'status',
    ];

    /**
     * Get the users for the OPD.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'opd_id');
    }
}
