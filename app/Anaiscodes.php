<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Anaiscodes extends Model
{
        protected $fillable = ['client_id','card_code','order_id'];



    public function client()
    {
        return $this->belongsTo(Client::class,'client_id','id');

    }//end of cards
    
        public function orders()
    {
       return $this->belongsTo(Order::class,'order_id','id');
        
    
    }//end of cards

}