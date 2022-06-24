<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tdawalDetails extends Model
{
        protected $table = 'tdawal_details';

        protected $fillable = ['client_email','client_id','transaction_id','custom_ref','url','result','amount','store_id','our_ref','payment_method','customer_phone','card_id','client_name','order_id','charges','net_amount'];
}
