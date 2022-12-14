<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use App\Models\InventoryManagement;

class Ticket extends Model
{
    use HasApiTokens, HasFactory, Notifiable;    
    protected $table="tickets";   
    
    public function user() {
    return $this->belongsTo(User::class,'user_id', 'id');
    }
    
    public function inventoryManagement() {
    return $this->belongsTo(InventoryManagement::class,'inventory_id', 'ID');
    }
}