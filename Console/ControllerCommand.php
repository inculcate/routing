<?php
/**
 * @author      Amit Roy <amit@softflies.com>
 * @copyright   Copyright (c), 2021 Amit Roy
 * @license     MIT public license
 */
namespace Inculcate\Routing\Console;

use Inculcate\Console\ArgvInput;
use Inculcate\Console\ConsoleStarter;
use Inculcate\Console\PhpMaker;

/**
 * Class Command.
 */
trait ControllerCommand
{   
    use ConsoleStarter;

    /**
     * To list the name of the application 
     * @var array
     * @return Inculcate\Routing\Console\ControllerCommand\stub
     */
    private string $stub = "\stubs\controller.normal.stub";
    /**
     * To set the namespace of the file
     * @var string
     * @return Inculcate\Routing\Console\ControllerCommand\namespace
     */
    private string $namespace = "App\Http\Controllers";
    /**
     * To set the controllerName of the file
     * @var string
     * @return Inculcate\Routing\Console\ControllerCommand\controllerName
     */
    private string $controllerName;

    /**
     * We assume that controller path has [0,1,2] 
     * The controllerPath of the file of the application
     * @var string
     * @return Inculcate\Routing\Console\ControllerCommand\controllerPath
     */
    private string $controllerPath = "App\Http\Controllers\\";

    /**
     * To set the description of the file
     * @var string
     * @return Inculcate\Routing\Console\ControllerCommand\description
     */
    private string $description = "Generate the controller"; 

    /**
    * To set the namespace of the scripts
    * @param null
    * @method getNamespace
    * @return Inculcate\Foundation\Console\PhpMaker\getNamespace
    */
    private function getNamespace() : string {
       
       return $this->namespace;

    }

    /**
    * we set the controller directory, controller's name and namespace
    * retun the controller file path
    * @param null
    * @method getControllerReday
    * @return Inculcate\Foundation\Console\PhpMaker\getControllerReday
    */
    private function getControllerReday () : string {
      //we get signatures values into an array
      // make sure that signatures has some directorty, creation requested
      // we wil check that directory exists or not
      $console = explode("/",$this->getRequestedConsoleValue(2));
      // get the constroller name
      $this->controllerName = end($console); 
      // remove the controller name from the directiry
      unset($console[key($console)]);
      // set the controller namespace
      if (implode("\\", $console) !==""){
         $this->namespace = $this->namespace."\\".implode("\\", $console);
      }
      // set the controller directry path
      // what if the cntroller directry does not exists
      // we then create it
      $directiry = base_path($this->controllerPath.implode("\\", $console));
      if (!file_exists($directiry)) {
          mkdir($directiry, 0777, true);
      }

      return $directiry."\\".$this->controllerName.".php";

    }
    /**
    * To get the controller stub path
    * @param null
    * @method getControllerStubPath
    * @return \Inculcate\Foundation\Console\PhpMaker\getControllerStubPath
    */
    private function getControllerStubPath() : string {
       
       return file_exists($customPath = base_path(trim("vendor\\softflies\\framework\\src\\Inculcate\\Routing\\Console".$this->stub)))
                        ? $customPath
                        : __DIR__.$this->stub;

    }
    /**
    * we create the normal controll here
    * @param null
    * @method makeNormalController
    * @return \Inculcate\Foundation\Console\makeNormalController
    */
    public function makeNormalController(){
       
       $controllerPath = $this->getControllerReday();
       // conntroller does not exixts
       if (!file_exists($controllerPath)) {
           $contents = PhpMaker::setFileContent($this->getControllerStubPath(),FILE_IGNORE_NEW_LINES)
                   ->textReplace("{{ namespace }}",$this->getNamespace())
                   ->textReplace("{{ class }}",$this->controllerName)
                   ->get();

           PhpMaker::setScripts($contents)->setScriptsPath($controllerPath)->MakeFile();
           exit("\e[0;32;40mController created successfully!\e[0m\n");
       }
       // controller alredy exists
       else{
           exit("\e[0;31;40mController already exists!\e[0m\n");
       }
       
       
    }

} 