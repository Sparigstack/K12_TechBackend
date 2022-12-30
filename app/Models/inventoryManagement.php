<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class inventoryManagement extends Model
{
    use HasFactory;
   protected $table="inventory_management"; 
   
   public function student() {
    return $this->belongsTo(Student::class, 'ID','Inventory_ID');
    }
}
