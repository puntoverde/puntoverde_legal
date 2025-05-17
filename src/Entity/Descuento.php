<?php
namespace App\Entity;
use Illuminate\Database\Eloquent\Model;

class Descuento extends Model 
{
    protected $table = 'descuento';
    protected $primaryKey = 'iddescuento';
    public $timestamps = false;
}