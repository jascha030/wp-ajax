<?php

namespace Jascha030\WP\Ajax;

use Jascha030\WP\Ajax\Script\AjaxScriptFile;

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
     * @var array AjaxScriptFile
     */
    protected $scripts;

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
     * WpAJax constructor.
     *
     * @param AjaxScriptFile|null $scriptFile
     * @param bool $nopriv
     * @param bool $hook
     * @param array $excludeMethods
     *
     * @throws ReflectionException
     */
    public function __construct(
        AjaxScriptFile $scriptFile = null,
        bool $nopriv = true,
        bool $hook = false,
        array $excludeMethods = []
    ) {
        $this->reflector = new ReflectionClass($this);

        $this->scripts[]      = $scriptFile;
        $this->nopriv         = $nopriv;
        $this->excludeMethods = array_merge(['hook', 'setAjaxScript'], $excludeMethods);

        if ($hook) {
            $this->hook();
        }
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
    }

    /**
     * Enqueue ajax scripts with wordpress
     */
    public function enqueueScripts()
    {
        foreach ($this->scripts as $script) {
            /** @var AjaxScriptFile $script */
            $script->enqueue();
        }
    }
}
