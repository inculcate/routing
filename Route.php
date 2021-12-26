<?php
/**
 * @author      Amit Roy <amit@softflies.com>
 * @copyright   Copyright (c), 2021 Amit Roy
 * @license     MIT public license
 */
namespace Inculcate\Routing;

use ReflectionMethod;
use ReflectionException;
/**
 * Class Route.
 */
class Route
{   
    /**
    * @var string
    * @return Inculcate\Routing\Route\httpBasePath
    */
    protected static $httpBasePath;
    /**
    * @var bool
    * @return Inculcate\Routing\Route\routeNotFound
    */
    protected static $routeNotFound = TRUE;
    /**
    * @var string
    * @return Inculcate\Routing\Route\controller
    */
    protected static $controller = "controller";
    /**
    * @var string
    * @return Inculcate\Routing\Route\method
    */
    protected static $method = "method";
    /**
    * @var string
    * @return Inculcate\Routing\Route\type
    */
    protected static $type="type";
    /**
    * @var string
    * @return Inculcate\Routing\Route\httpsType
    */
    protected static $httpsType;
    /**
    /**
    * @var string
    * @return Inculcate\Routing\Route\name
    */
    protected static $name="name";
    /**
    * @var string
    * @return Inculcate\Routing\Route\prefix
    */
    protected static $prefix='';
    /**
    * @var const
    * @return Inculcate\Routing\Route\PREFIX
    */
    protected const PREFIX="prefix";
    /**
    * @var const
    * @return Inculcate\Routing\Route\WHERE
    */
    protected const WHERE="where";
    /**
    * @var array | null
    * @return Inculcate\Routing\Route\where
    */
    protected static $where;
    /**
    * @var string, array
    * @return Inculcate\Routing\Route\group
    */
    protected static $group;
    /**
    * @var string
    * @return Inculcate\Routing\Route\get
    */
    protected static $get = "GET";

    /**
    * @var string
    * @return Inculcate\Routing\Route\post
    */
    protected static $post = "POST";

    /**
    * @var string
    * @return Inculcate\Routing\Route\requestedMethod
    */
    protected static $requestedMethod;
    /**
    * @var array
    * @return Inculcate\Routing\Route\getHttps
    */
    protected static $getHttps=[];
    /**
    * @var array
    * @return Inculcate\Routing\Route\postHttps
    */
    protected static $postHttps=[];

    /**
    * @var string
    * @return Inculcate\Routing\Route\routeName
    */
    protected static $routeName;
    /**
    * @var string
    * @return Inculcate\Routing\Route\pattern
    */
    protected static $pattern;
    /**
    * @var string
    * @return Inculcate\Routing\Route\fn
    */
    protected static $fn;
    /**
    * @var (array)
    * @return Inculcate\Routing\Route\https
    */
    protected static $https = [];

    

    /**
    * @param string | null, $where
    * @method where
    * @return Inculcate\Routing\Route\where
    */
    public static function where($where=null){
       $httpsType= self::$httpsType;
       if ($where!=="" && array_key_exists(self::$pattern,self::$$httpsType)) {
           self::$$httpsType[self::$pattern][self::WHERE]=$where;
       }
       return new static;
    }	
    /**
    * @param null
    * @method https
    * @return Inculcate\Routing\Route\https
    */
    public static function https(){

       self::$https=array_merge([self::$get=>self::$getHttps],[self::$post=>self::$postHttps]); 
       return self::$https;

    }

    /**
    * @param (array), string | null
    * @method group
    * @return Inculcate\Routing\Route\prefix
    */
    public static function group($group=null,$callback=null){
        
        self::$group=$group;

        if (is_array(self::$group)) {
            self::$prefix=self::$group[self::PREFIX] ?? null;
        }

        if (is_callable($callback)) {
            call_user_func($callback);
            self::$prefix='';// we need to set prefix, to use it agian 
        }

        return new static;

    }

    /**
    * @param string|null, $name
    * @method name
    * @return Inculcate\Routing\Route\name
    */
    public function name($name=""){
       
        $httpsType= self::$httpsType;

        if ($name!=="" && array_key_exists(self::$pattern,self::$$httpsType)) {
           self::$$httpsType[self::$pattern][self::$name]=$name;
        }
         
        return new static; 
    }

	/**
    * @param string, $pattern, $fn
	* @method get
    * @return Inculcate\Routing\Route\get
	*/
	public static function get($pattern='',$fn){
      
      self::$requestedMethod=self::$get;
      self::$pattern=$pattern;  
      self::$fn=$fn;
      self::$httpsType="getHttps";  
      self::setRoute();
      return new static;

	}
    /**
    * @param string, $pattern, $fn
    * @method post
    * @return Inculcate\Routing\Route\post
    */
    public static function post($pattern='',$fn){
      
      self::$requestedMethod=self::$post;
      self::$pattern=$pattern;  
      self::$fn=$fn;
      self::$httpsType="postHttps";  
      self::setRoute();
      return new static;

    }
    /**
    * @param null
    * @method setRoute
    * @return Inculcate\Routing\Route\setRoute
    */
    protected static function setRoute(){
        
        // set prefix of the routes
        self::setPrefix();
        $httpsType = self::$httpsType;
        // if the route($fn), has socend param has an array of Controller and it's method.. 
        if (is_array(self::$fn)) { 
            
            self::$$httpsType[self::$pattern] = [
                self::$controller=>self::$fn[0] ?? null,
                self::$method=>self::$fn[1] ?? null,
                self::$type=>self::$requestedMethod ?? null,
            ];
        
        }
        //if the route($fn) is clallable to set the callable
        elseif (is_callable(self::$fn)) {
            self::$$httpsType[self::$pattern] = [
                self::$controller => self::$fn,
                self::$type => self::$requestedMethod ?? null,
            ];
        }
    }
    /**
    * @param null
    * @method setPrefix
    * @return Inculcate\Routing\Route\setPrefix
    */
    protected static function setPrefix(){
        
        if (self::$prefix!=='' && self::$prefix!=="/") {
            self::$pattern="/".self::$prefix.self::$pattern;
        }

    }
    /**
    * To register the files
    * @param string | null , $file_path
    * @method register
    * @return Inculcate\Routing\Route\register
    */
    public static function register($file_path=null){
         
         if ($file_path!==null) {
            file_load($file_path);
         }

    }

    /**
     * @param null
     * @method getRequestedMethod
     * @return Inculcate\Routing\Route\getRequestedMethod
     */
    private static function getRequestedMethod(){

        $method = $_SERVER['REQUEST_METHOD'];

        if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
          //  ob_start(); // to handle the output buffer
            $method = 'GET';
        }

        // We need to over ride the header when the SERVER
        elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $headers = self::getRequestedHeaders();
            if (isset($headers['X-HTTP-Method-Override']) && in_array($headers['X-HTTP-Method-Override'], array('PUT', 'DELETE', 'PATCH'))) {
                $method = $headers['X-HTTP-Method-Override'];
            }
        }

        return strtoupper($method);
    }

    /**
     * @param null
     * @method getRequestedHeaders
     * @return Inculcate\Routing\Route\getRequestedHeaders
     */
    private static function getRequestedHeaders(){
        $headers = array();

        foreach ($_SERVER as $name => $value) {
            if ((substr($name, 0, 5) == 'HTTP_') || ($name == 'CONTENT_TYPE') || ($name == 'CONTENT_LENGTH')) {
                $headers[str_replace(array(' ', 'Http'), array('-', 'HTTP'), ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        return $headers;
    }
    
    /**
    * @param null
    * @method boom
    * @return Inculcate\Routing\Route\boom
    */
    public static function boom(){
         
        return self::shootRoute();

    }
    
    /**
    * @param null
    * @method getHttpBasePath
    * @return Inculcate\Routing\Route\getHttpBasePath
    */
    private static function getHttpBasePath(){

        if (self::$httpBasePath === null) {
            self::$httpBasePath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
        }

        return self::$httpBasePath;

    }

    /**
    * @param null
    * @method getRequestedUri
    * @return Inculcate\Routing\Route\getRequestedUri
    */
    private static function getRequestedUri(){

        $uri = substr(rawurldecode($_SERVER['REQUEST_URI']), strlen(self::getHttpBasePath()));
        
        // We don't include the query
        if (strstr($uri, '?')) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }

        return '/' . trim($uri, '/');
    }

    /**
     * @param null
     * @method shootRoute
     * @return Inculcate\Routing\Route\shootRoute
     */
    private static function shootRoute(){
       
       //print_r(self::https());
        if (array_key_exists(self::getRequestedMethod(), self::https())) {
            // make sure requested uri exists or not into the routes
            if (array_key_exists(self::getRequestedUri(), self::https()[self::getRequestedMethod()])) {
                // finding the pattern of the requested uri into the routes
                $controller = self::https()[self::getRequestedMethod()][self::getRequestedUri()][self::$controller];
                // when controller is not a call back function
                if (\is_string($controller)) {
                    $method = self::https()[self::getRequestedMethod()][self::getRequestedUri()][self::$method];
                    // make sure by using try catch 
                    //to handle the controller and method's error, handeling 
                    try{
                        //we need to check the called controller and method..
                        // whether the controller and method are what they are..
                        $reqControllerMathod = new ReflectionMethod($controller, $method);
                        // get the controller instance
                        $controller = new $controller();  
                        // requested method has to be public
                        if ($reqControllerMathod->isPublic() && (!$reqControllerMathod->isAbstract())) {
                             // invoking the object and method  
                             return call_user_func(array($controller,$method));
                           
                        } 
        
                    } 
                    //we handle the error whilst running the controller and it's method..
                    catch ( ReflectionException $reflectionException ){
                       print($reflectionException->getMessage()); exit;
                    } 
                    
                }
                // else when controller is callable
                elseif (is_callable($controller)) {
                       // when the controller is a callback inspite of the namespace of the controller
                       return call_user_func($controller);
                    
                }

            }
            //Route not found
            else{
                print("Requested route ".self::getRequestedUri()." not found"); exit;
            }
            
        }
    }
}