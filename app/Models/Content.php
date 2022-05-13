<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Content extends Model{
    /**
     * 1. Relación con user.
     * 2. Relación con categorías.
     * 3. Imágenes del contenido.
     * 4. Accesor imagen destacada.
     * 5. Accesor imagen thumbnail.
     * 6. Importación de contenidos desde .json.
     */
    
    use HasFactory;
    use SoftDeletes;

    protected $table = 'contents';

    protected $fillable = ['name', 'code', 'referrer', 'type', 'title', 'slug', 'excerpt', 'content', 'tags', 'links', 'video', 'classes', 'user_id', 'status', 'observations', 'published_at', 'published_end'];

    protected $dates = ['deleted_at']; //Columna de borrado lógico

    /**
     * 1. Relación con user.
     */
    public function user(){
        return $this->belongsTo(User::class);
    }

    /**
     * 2. Relación con categorías.
     */
    public function categories($content){
        $categories = CategoryItem::select('categories.*')
            ->join('categories', 'category_items.category_id', '=', 'categories.id')
            ->join('modules', 'category_items.module_id', '=', 'modules.id')
            ->where('modules.slug', 'contents')
            ->where('category_items.item_id', $content)
            ->get();

        return $categories; 
    }

    /**
     * 3. Imágenes del contenido.
     */
    public function images(){
        return $this->hasMany(ContentImage::class);
    }

    /**
     * 4. Accesor imagen destacada.
     */
    public function getFeaturedImageUrlAttribute(){
        $featuredImage = $this->images()->where('featured', true)->first();

        //si no hay imagen destacada:
        if(!$featuredImage){
            $featuredImage = $this->images()->first();
        }

        if($featuredImage){
            return $featuredImage->image;
        }

        //Imagen por defecto si no hay imágenes asociadas:
        return 'default.jpg';
    }

    /**
     * 5. Accesor imagen thumbnail.
     */
    public function getThumbnailImageUrlAttribute(){
        $featuredImage = $this->images()->where('thumbnail', true)->first();

        //si no hay imagen destacada:
        if(!$featuredImage){
            $featuredImage = $this->images()->first();
        }

        if($featuredImage){
            return $featuredImage->image;
        }

        //Imagen por defecto si no hay imágenes asociadas:
        return 'default.jpg';
    }

    /**
     * 6. Importación de contenidos desde .json.
     */
    public static function import(){
        $exist = Content::select('id')->first();

        if(!$exist){
            $path = storage_path()."/json/contents.json";
            $contents = json_decode(file_get_contents($path), true); 

            $path_images = storage_path()."/json/content-images.json";
            $images = json_decode(file_get_contents($path_images), true);

            //Truncate de la tabla:
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('content_images')->truncate();
            DB::table('contents')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            foreach($contents as $c){
                Content::create([
                    'name' => $c['name'],
                    'code' => $c['code'],
                    'referrer' => $c['referrer'],
                    'type' => $c['type'],
                    'title' => $c['title'],
                    'slug' => $c['slug'],
                    'excerpt' => $c['excerpt'],
                    'content' => $c['content'],
                    'tags' => $c['tags'],
                    'links' => $c['links'],
                    'video' => $c['video'],
                    'classes' => $c['classes'],
                    'user_id' => $c['user_id'],
                    'status' => $c['status'],
                    'observations' => $c['observations'],
                    'published_at' => $c['published_at'],
                    'published_end' => $c['published_end']
                ]);

                //Obtenemos las imágenes del contenido y volvemos a guardarlas:
                if($images){
                    foreach($images as $i){
                        if(isset($i['content_id']) && $c['id'] == $i['content_id']){
                            ContentImage::create([
                                'image' => $i['image'],
                                'content_id' => $c['id'],
                                'featured' => $i['featured'],
                                'thumbnail' => $i['thumbnail']
                            ]);    
                        }
                    }
                }
            }
        }
    }
}
