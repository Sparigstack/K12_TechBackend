<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class inventoryManagement extends Model
{
    use HasFactory;
  protected $casts = [
    'Purchase_date'  => 'date:m-d-Y', 
    'OEM_warranty_until' => 'date:m-d-Y',
    'Extended_warranty_until' =>'date:m-d-Y',
    'ADP_coverage' =>'date:m-d-Y'
];
    
}
