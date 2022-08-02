<?php
/** (c) Joachim Göddel . RLMS */
namespace App\App;

# Klasse
class Router
{
    private Container $container;

    # CONSTRUCT
    public function __construct(Container $container){
        $this->container = $container;
    }

    public function add($ctrl, $function)
    {
        $container = $this->container->build($ctrl);
        $view = $function;
        $this->build($container, $view);
    }

    public function build($container, $view)
    {
        $container->$view();
    }
}