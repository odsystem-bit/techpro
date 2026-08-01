<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormationModuleFile extends Model
{
    protected $fillable = [
        'formation_module_id',
        'file_path',
        'original_name',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function module()
    {
        return $this->belongsTo(FormationModule::class);
    }
}
