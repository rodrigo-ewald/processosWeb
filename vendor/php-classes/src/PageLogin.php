<?php

namespace saes;

class PageLogin extends Page
{

  public function __construct($options = array(), $tpl_dir = DIRECTORY_SEPARATOR.'processos'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'login'.DIRECTORY_SEPARATOR)
  {
    parent::__construct($options, $tpl_dir);
  }

}
