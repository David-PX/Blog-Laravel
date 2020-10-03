<?php

namespace App\Http\Controllers;

use Illiminate\Http\Request;
use App\Post;
use App\Category;

class PruebasController extends Controller
{
    public function index()
    {

        $titulo = 'Animales';
        $animales = ['Perro', 'Gato', 'Tigre'];
        return view('pruebas.index', array(
            'titulo' => $titulo,
            'animales' => $animales));
    }



    public function testOrm()
    {
     /*   $posts = Post::all();
        foreach($posts as $post){

           echo "<h1>". $post->title."</h1>";
           echo "<p>".$post->content."</p>";
           echo "<span>{$post->user->name} - {$post->category->name}</span>";
           echo "<br>";

        }*/

        $categories= Category::all();
        foreach($categories as $category){
            echo "<h1>{$category->name}</h1>";
            foreach($category->posts as $post){

           echo "<h1>". $post->title."</h1>";
           echo "<p>".$post->content."</p>";
           echo "<span>{$post->user->name} - {$post->category->name}</span>";
           echo "<br>";

        }
        echo '<hr>';
            

        }
       


        die();
    }
}
