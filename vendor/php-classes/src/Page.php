<?php

namespace saes;

use Rain\Tpl;

class Page
{

    private $tpl;
    private $options = [];
    private $defaults = ["header"=>true,"footer"=>true,"data"=>[]];

    public function __construct($options = array(), $tpl_dir = DIRECTORY_SEPARATOR.'processos'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR)
    {
        $this->options = array_merge($this->defaults, $options);
        $config = array(
          "tpl_dir"=>$_SERVER["DOCUMENT_ROOT"].$tpl_dir,
          "cache_dir"=>$_SERVER["DOCUMENT_ROOT"].DIRECTORY_SEPARATOR.'processos'.DIRECTORY_SEPARATOR.'views-cache'.DIRECTORY_SEPARATOR,
          "debug"=>"true"
        );
        Tpl::configure($config);
        $this->tpl = new Tpl();
        $this->setData($this->options["data"]);
        if ($this->options["header"] === true) $this->tpl->draw("header");
    }

    public function setTpl($name, $data = array(), $returnHTML = false)
    {
        $this->setData($data);
        return $this->tpl->draw($name, $returnHTML);
    }

    public function __destruct()
    {
        if ($this->options["footer"] === true) $this->tpl->draw("footer");
    }

    private function setData($data = array()): void
    {
        foreach ($data as $key => $value) {
            $this->tpl->assign($key, $value);
        }
    }

}
