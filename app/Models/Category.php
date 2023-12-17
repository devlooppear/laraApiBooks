<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name'];

    /**
     * Get the books associated with the category.
     */
    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
