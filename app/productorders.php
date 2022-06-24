<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class productorders extends Model
{
   protected $connection = 'products';
    protected $table = 'order';
   
    protected $fillable = [
        'address_id',
       'arrival_date',
       'create_date' ,
        'discount',
        'order_number',
        'promocodeid',
        'total',
        'userid',
       
    ];

    






}
