<?php

namespace Jascha030\WP\Ajax;

use Jascha030\WP\Ajax\Script\AjaxScriptConfig;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * Class WpAJax
 *
 * @package Jascha030\WP\Ajax
 */
class WpAjax
{
    /**
     * @var AjaxScriptConfig
     */
    protected $config;

    /**
     * @var ReflectionClass
     */
    protected $reflector;

    /**
     * @var array
     */
    protected $excludeMethods;

    /**
     * @var bool
     */
    protected $nopriv;

    /**
     * @var array
     */
    protected $callables = [];

    /**
     * WpAJax constructor.
     *
     * @param AjaxScriptConfig|null $jsConfig
     * @param bool $nopriv
     * @param bool $hook
     * @param array $excludeMethods
     *
     * @throws ReflectionException
     */
    public function __construct(
        AjaxScriptConfig $jsConfig = null,
        bool $nopriv = true,
        bool $hook = true,
        array $excludeMethods = []
    ) {
        $this->reflector = new ReflectionClass($this);

        $this->config         = $jsConfig;
        $this->nopriv         = $nopriv;
        $this->excludeMethods = array_merge(['hook', 'addAjaxScript'], $excludeMethods);

        if ($hook) {
            $this->hook();
        }
    }

    public function addCallable(Callable $callable)
    {
        $this->callables[] = $callable;
    }

    /**
     * Hook al non-magic and non-excluded methods of this class to wp_ajax_ hooks.
     *
     * When a javascript config is added this method will also localize and enqueue the script.
     */
    public function hook()
    {
        $methods = $this->reflector->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            // Check if method is not excluded or magic
            if (! in_array($method->name, $this->excludeMethods) && strpos($method->name, '__') === 0) {
                $argumentsNum = $method->getNumberOfParameters();

                add_action("wp_ajax_{$method}", [$this, $method], $argumentsNum);

                if ($this->nopriv) {
                    add_action("wp_ajax_nopriv_{$method}", [$this, $method], $argumentsNum);
                }
            }
        }

        // Hook manually added callable methods/functions
        if (!empty($this->callables)) {
            foreach ($this->callables as $c) {
                if (is_array($c)) {
                    add_action("wp_ajax_{$c[1]}", $c);
                } else {
                    add_action("wp_ajax_{$c}", $c);
                }
            }
        }

        // Hook config method to enqueue script.
        if ($this->config) {
            add_action('wp_enqueue_scripts', [$this->config, 'hook']);
        }
    }
}
