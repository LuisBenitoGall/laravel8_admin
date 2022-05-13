<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentImage extends Model{
    /**
     * 1. Imágenes de contenido.
     * 2. Imágenes por código de contenido.
     * 3. Imagen principal.
     */
    
    use HasFactory;

    protected $table = 'content_images';

    protected $fillable = ['image', 'content_id', 'featured', 'thumbnail'];

    /**
     * 1. Imágenes de contenido.
     */
    public function content(){
        return $this->belongsTo(Content::class);
    }

    /**
     * 2. Imágenes por código de contenido.
     */
    public static function getImagesContent($code){
        $images = ContentImage::select('content_images.image')
        ->join('contents', 'content_images.content_id', '=', 'contents.id')
        ->where('contents.code', $code)
        ->get();

        return $images;
    }

    /**
     * 3. Imagen principal.
     */
    public static function getFeaturedImage($id){
        $image = ContentImage::where('content_id', $id)
        ->where('featured', 1)
        ->first();

        if($image == NULL){
            $image = ContentImage::where('content_id', $id)
            ->first();  
        }

        return $image;
    }
}
