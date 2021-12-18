<?php
/**
 * @author      Amit Roy <amit@softflies.com>
 * @copyright   Copyright (c), 2021 Amit Roy
 * @license     MIT public license
 */
namespace Inculcate\Routing;

use Inculcate\View\View;
use Inculcate\Routing\Route;
/**
 * Class Response.
 */
class Response
{   
   /**
   * @param null
   * @method makeResponse
   * @return Inculcate\Routing\makeResponse
   */
   protected function makeResponse(){
      
      $view=Route::boom();  
      //make sure , we have the view requested
      if($view instanceof View){
         // wehen we have the render requested
      	 if ($view->is_render() === false) {      	 	
             $view->invokeView();
      	 } 	

      }

   }

}