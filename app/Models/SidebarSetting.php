<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SidebarSetting extends Model
{
    use HasFactory;

    protected $fillable = ['users', 'products', 'inventory', 'categories', 'tags', 'all_orders', 'posts',
        'marketing', 'web_pages', 'zones', 'promotions', 'advertisement', 'inquiries'];
}
