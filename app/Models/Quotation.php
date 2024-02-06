<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = ['reference_number','user_id','customer_name','company_name','address','email_address','quotation_total','weight'];

    public function quotationItems(){
        return $this->hasMany(QuotationItem::class);
    }

    public static function getQuotationsForFilters($searchKey){

        return Quotation::with('quotationItems')
        ->where('reference_number','like','%'.$searchKey.'%')
        ->orWhere('customer_name','like','%'.$searchKey.'%')
        ->orWhere('company_name','like','%'.$searchKey.'%')
        ->orWhere('email_address','like','%'.$searchKey.'%')
        ->orWhere('address','like','%'.$searchKey.'%')->paginate(env("RECORDS_PER_PAGE"));
    }
}
