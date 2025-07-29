<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author_id',
        'category_id',
        'publisher_id',
        'published_year',
        'cover_image',
    ];

    public function getCoverImageUrlAttribute()
    {
        if ($this->cover_image && Storage::disk('public')->exists($this->cover_image)) {
            return Storage::disk('public')->url($this->cover_image);
        }
        return Storage::disk('public')->url('images/no_imageF.jpeg');
    }

    protected static function booted()
    {
        static::deleting(function ($book) {
            if ($book->cover_image && $book->cover_image !== 'images/no_imageF.jpeg') {
                Storage::disk('public')->delete($book->cover_image);
            }
        });
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'borrowings')
                    ->withPivot('id', 'borrowed_at', 'returned_at')
                    ->withTimestamps();
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }
    
}