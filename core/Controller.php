<?php
  class Controller{

    protected $P, $R, $G, $rendere, $errors, $data, $error_fields;
    
    public function __filter_request(){
      return true;
    }

    public static function newInstance(){
      return new static();
    }
    public function filter_failed(){
      die("Request did not pass filter");
    }
    
    public function getErrors(){
      return $this->errors;
    }

    public function headers($file = false){
      ($file)? $this->render($file) : $this->render("_partial/headers");
    }

    public function footers($file = false){
      ($file)? $this->render($file) : $this->render("_partial/footers");
    }

    public function setErrorsFields($e){
      $this->error_fields = $e;
    }

    public function getErrorFields(){
      return $this->error_fields;
    }
    protected function putError($message){
      array_push($this->errors, $message);
    }

    public function showErrors(){
      if(count($this->errors) > 0){
        echo "<ul>";
        foreach ($this->errors as $error) {
          echo "<li>$error</li>";
        }
        echo "</ul>";
      }
    }

    public function __construct(){
      $this->R = $_REQUEST;
      $this->G = $_GET;
      $this->P = $_POST;
      $this->rendered = false;
      $this->errors = array();
      $this->onCreate();
    }

    protected function onCreate(){}

    protected function render($file){
      if($file == "" || $file == false) { 
        throw new Exception(" Not a valid view");
        return;
      }

      $controller = $this;
      $class = substr(get_called_class(),0,-10);

      $pices = explode("/", $file);

      $path = "";
      if(count($pices) > 1){
        $path = 'views/'.$file.".php";
      }elseif(count($pices) == 1){
        $path = 'views/'.$class."/".$file.".php";
      }

      if(file_exists($path)){
        include $path;
        $this->rendered = true;
      }else throw new Exception("No views found: $file");
    }

    public function getRenderState(){
      return $this->rendered;
    }

    public function a_tag($text, $link){
      echo "<a href='$link'>$text</a>";
    }

    public function now(){
      return  date("Y-m-d H:i:s");
    }

    public function todate(){
      return date("Y-m-d");
    }

    protected function p($var){
      echo "<pre>";
      var_dump($var);
      echo "</pre>";
    }

    public function setData($data){
      $this->data = $data;
    }

    public function setErrors($error){
      $this->errors = $error;

    }
      
    public function getData(){
      return $this->data;
    }

    public function getRequestErrors(){
      return $this->errors;
    }

    public function is_valid_request(){
      return (count($this->errors) == 0);
    }
  }
?>