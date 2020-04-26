<?php

namespace Jascha030\WP\Ajax\Provider;

use Jascha030\WP\Ajax\Script\AjaxScriptFile;
use Jascha030\WP\Ajax\WpAjax;
use Jascha030\WP\Subscriptions\Provider\ActionProvider;
use Jascha030\WP\Subscriptions\Provider\Provider;
use ReflectionException;
use ReflectionMethod;

if (! defined('ABSPATH')) {
    die("Forbidden");
} // Abandon ship.

if (!class_exists('Jascha030\WP\Ajax\Provider\WpAjaxProvider')) {
    /**
     * Class WpAjaxProvider
     *
     * For usage with the jascha030\wp-subscriptions package
     *
     * @package Jascha030\WP\Ajax\Provider
     */
    class WpAjaxProvider extends WpAjax implements ActionProvider
    {
        USE Provider;

        public static $actions = [];

        /**
         * WpAjaxProvider constructor.
         *
         * @throws ReflectionException
         */
        public function __construct()
        {
            parent::__construct();

            foreach ($this->reflector->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                // Check if method is not excluded or magic
                if (! in_array($method->name, $this->excludeMethods) && strpos($method->name, '__') === 0) {
                    $argumentsNum = $method->getNumberOfParameters();

                    self::$actions["wp_ajax_{$method}"] = [[$this, $method], $argumentsNum];

                    if ($this->nopriv) {
                        self::$actions["wp_ajax_nopriv_{$method}"] = [[$this, $method], $argumentsNum];
                    }
                }
            }
        }

        /**
         * @param AjaxScriptFile $script
         */
        public function setAjaxScript(AjaxScriptFile $script)
        {
            $this->scripts[$script->getHandle()] = $script;
        }
    }
}
