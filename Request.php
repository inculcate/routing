<?php
/**
 * @author      Amit Roy <amit@softflies.com>
 * @copyright   Copyright (c), 2021 Amit Roy
 * @license     MIT public license
 */
namespace Inculcate\Routing;
/**
 * Class Request.
 */
trait Request
{   
    /**
     * @var string to set name of the requested https
     */
    protected $httpsName;

    /**
     * @var string to set name of the requested https
     */
    protected $callInvokeHttps;

    /**
     * @var array to be return of the requeted parameter
     */
    protected $requestParam =  array();

    /**
     * @var boolean to set name of the requested https (Found || Not)
     */
    protected $routeNotFound = TRUE;
    /**
     * @var string , to set server base path of the requested https
     */
    protected $getHttpBasePath;
    /**
     * @var string , to set the requested route method ( http ), whilst setting the pattern ( route )
     */
    private $reqRouteMethod = '';

    /**
     * @var string , to set the requested route method's controller ( http )
     */
    private $getRequestedHttpController = '';

    /**
     * @var string , to set the requested route controller's method ( http )
     */
    private $getRequestedHttpMethod = '';

    /**
     * @var array of, to set all the requested https' VERB 
     */
    protected $https = array();

    /**
     * @var array of, to set all the requested GET ( VERB ) https
     */
    protected $getHttps = array();

    /**
     * @var array of, to set all the requested POST ( VERB ) https
     */
    protected $postHttps = array();

    /**
     * @var array of, to set all the requested PUT ( VERB ) https
     */
    protected $putHttps = array();

    /**
     * @var array of, to set all the requested DELETE ( VERB ) https
     */
    protected $deleteHttps = array();

    /**
     * @var string of, the requested http's namespace
     */
    protected $httpPrefix = '';

    /**
     * It helps the application to invoke
     *
     * @return \SrAmitRoy\Http\Request\invoke
     */
    private function invoke($pattern=''){ 
       
       if ($this->getRequestedMethod()==="GET") {
           // first we need to set it here, so that we can call what routes array tob called
           $this->callInvokeHttps = "getHttps";
           // call this method to invoke the requested route accordingly
           $this->callInvoke($pattern);          
       }

       // call the POST requested
       elseif ($this->getRequestedMethod()==="POST") {
           // first we need to set it here, so that we can call what routes array tob called
           $this->callInvokeHttps = "postHttps";
           // call this method to invoke the requested route accordingly
           $this->callInvoke($pattern);          
       }

       // buffer output cleans
       if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
          ob_end_clean();
       }

       //if ruote not matched
       if ($this->routeNotFound) {

           $this->route404();
       }

    }

    /**
     * It helps 404 route not found
     *
     * @return \SrAmitRoy\Http\Request\route404
     */
    private function route404(){

        die("route not found");

    }

    /**
     * It helps you the  requested Http's lists
     *
     * @return \SrAmitRoy\Http\Request\callInvoke
     */
    private function callInvoke($pattern=''){
        
        $routes= $this->callInvokeHttps;

        if ($routes !=='' && array_key_exists($pattern, $this->$routes)) {
             
             // assinging the pattern's request from the routes
             $pattern = $this->$routes[$pattern];
             
             // if the requested patteren has callback function
             if (is_callable($pattern)) {
                //invoking the callback function 
                call_user_func_array($pattern, $this->requestParam);
                $this->routeNotFound = FALSE;
             }
             
             // if the requested pattern has controller and method
             elseif (is_array($pattern)) {
                
                 // getting the controller name space from pattern
                 $this->getRequestedHttpController = "\\".$pattern['controller'];
                 // getting the controller's method from patteren
                 $this->getRequestedHttpMethod = $pattern['method'];
                 
                 // we need to make sure that, controller and it's method are a valid string  
                 if (\is_string($this->getRequestedHttpController) && \is_string($this->getRequestedHttpMethod)) {
                    // we use try catch to handle the controller and method's error, handeling 
                    try{
                        //we need to check the called controller and method..
                        // whether the controller and method are what they are..
                        $reqControllerMathod = new \ReflectionMethod($this->getRequestedHttpController, $this->getRequestedHttpMethod);
                        // get the controller instance
                        $controller = new $this->getRequestedHttpController(); 
                        $method = $this->getRequestedHttpMethod; 
                        $params = $this->requestParam; 
                        // requested method has to be public
                        if ($reqControllerMathod->isPublic() && (!$reqControllerMathod->isAbstract())) {
                           // invoking the object and method  
                           call_user_func_array(array($controller,$method),$params);
                           // we set the route got found , when all went well 
                           $this->routeNotFound = FALSE;
                        } 
                        // we allow the static method 
                        elseif ($reqControllerMathod->isStatic()) {
                           // invoking the object and method  
                           forward_static_call_array(array($controller , $method),$params);
                           // we set the route got found , when all went well 
                           $this->routeNotFound = FALSE;
                        }

                    } 
                    //we handle the error whilst running the controller and it's method..
                    catch ( \ReflectionException $reflectionException ){
                      echo "<pre>"; print_r($reflectionException->getMessage()); die();
                    } // Ttry catch ends here
            
                 }// is string, check 'if' ends here

             }// pattern "else if" ends here
       
        }//routes chech , "if" ends here
               
    }

    /**
     * It sets you the (GET) requested Http's lists
     *
     * @return \SrAmitRoy\Http\Request\get
     */
    public function get($pattern='',$fn){
        
        // to set the req route method, so, we can set the https accordingliy
        $this->reqRouteMethod ="getHttps";
        // set the route
        $this->setRoute($pattern,$fn);
        
        return $this;
       
    }
    
    /**
     * It sets you the (POST) requested Http's lists
     *
     * @return \SrAmitRoy\Http\Request\post
     */
    public function post($pattern='',$fn){
        
        // to set the req route method, so, we can set the https accordingliy
        $this->reqRouteMethod ="postHttps";
        // set the route
        $this->setRoute($pattern,$fn);

        return $this;
       
    }

    /**
     * It sets you prefix of the requested Http's lists
     *
     * @return \SrAmitRoy\Http\Request\prefix
     */
    public function group($group=array(),$fn=''){

        // if the requested "$fn" is a callback function
        if (is_callable($fn)) {
            // set the prefix, of a route, whilst setting it 
            $this->httpPrefix = array_key_exists('prefix', $group) ? $group['prefix'] : '';
            //invoking the callback function 
            call_user_func_array($fn, [$this]);
        }

        //set the httpPrefix, we set it on setting the route 
        return $this;
       
    }
    
    /**
     * It sets you the requested Http's routes
     *
     * @return \SrAmitRoy\Http\Request\setRoute
     */
    private function setRoute($pattern='',$fn){
       
        $reqRouteMethod=$this->reqRouteMethod;
        $this->httpsName = $pattern;

        // prefix has to have some value, otherwise we can't accepts
        if($this->httpPrefix!=='' && $this->httpPrefix!=='/'){
           
           // we need to $uri to help the $pattern to be set properly
           $uri = '/'.$this->httpPrefix;
           //pattern has to also have some value, otherwise, we can't accespt
           if ($pattern!=='' &&$pattern!=='/') {
               
               $uri = $uri.$pattern;
           }
           
           // finnaly we get the pattern to be set..
           $pattern = $uri;

        }
        
        // if the route($fn), has socend param has an array of Controller and it's method.. 
        if (is_array($fn)) { 

           $controller = array_key_exists(0, $fn) ? $fn[0] : ''; 
           $method = array_key_exists(1, $fn) ? $fn[1] : ''; 
            
           if ($pattern!=='' && $controller!=='' && $method!=='') {
               $this->$reqRouteMethod[$pattern] = ['controller'=>$controller,'method'=>$method];
           }

        }

        //if the route($fn) is clallable to set the callable
        elseif (is_callable($fn) && $pattern!=='') {
            
               $this->$reqRouteMethod[$pattern] = $fn;
               $this->$reqRouteMethod['parameter'] = '';
          
        } 

    }

    /**
     * It sets you the (NAME) requested Https
     *
     * @return \SrAmitRoy\Http\Request\name
     */
    public function name($routeName=''){
        
        $reqRouteMethod = $this->reqRouteMethod;
        if($routeName!=='' && array_key_exists($this->httpsName,$this->$reqRouteMethod)){
           $this->$reqRouteMethod[$this->httpsName]['name']=$routeName;
        } 
    
    }
    
    /**
     * It gets all the requested $_SERVER's headers.
     *
     * @return \SrAmitRoy\Http\Request\getRequestedHeaders
     */
    private function getRequestedHeaders(){
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
     * @method method
     * @return Inculcate\Routing\Request\method
     */
    private function method(){

        $method = $_SERVER['REQUEST_METHOD'];

        if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
            ob_start(); // to handle the output buffer
            $method = 'GET';
        }

        // We need to over ride the header when the SERVER
        elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $headers = $this->getRequestedHeaders();
            if (isset($headers['X-HTTP-Method-Override']) && in_array($headers['X-HTTP-Method-Override'], array('PUT', 'DELETE', 'PATCH'))) {
                $method = $headers['X-HTTP-Method-Override'];
            }
        }

        return $method;
    }
    
    /**
     * @param null
     * @method getHttpBasePath
     * @return Inculcate\Routing\Request\getHttpBasePath
     */
    public function getHttpBasePath()
    {   
        if ($this->getHttpBasePath === null) {
            $this->getHttpBasePath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
        }

        return $this->getHttpBasePath;
    }

    /**
     * @param null
     * @method uri
     * @return Inculcate\Routing\Request\server_name
     */
    private function server_name(){

        $server_name = isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:'';

        return $server_name;
    }

    /**
     * @param null
     * @method uri
     * @return Inculcate\Routing\Request\uri
     */
    public function uri(){

        $uri = substr(rawurldecode($_SERVER['REQUEST_URI']), strlen($this->getHttpBasePath()));
        
        // We don't include the query
        if (strstr($uri, '?')) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }

        return '/' . trim($uri, '/');

    }
   
}