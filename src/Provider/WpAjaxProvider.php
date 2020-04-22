<?php

namespace Jascha030\WP\Ajax\Provider;

use Jascha030\WP\Ajax\script\AjaxScriptConfig;
use Jascha030\WP\Ajax\WpAjax;
use Jascha030\WP\Subscriptions\Provider\ActionProvider;
use Jascha030\WP\Subscriptions\Provider\Provider;
use ReflectionMethod;

/**
 * Class WpAjaxProvider
 *
 * @package Jascha030\WP\Ajax\Provider
 */
class WpAjaxProvider extends WpAjax implements ActionProvider
{
    USE Provider;

    public static $actions = [];

    public function __construct(
        AjaxScriptConfig $jsConfig = null,
        bool $nopriv = true,
        array $excludeMethods = []
    ) {
        parent::__construct($jsConfig, $nopriv, false, $excludeMethods);

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

        // Hook config method to enqueue script.
        if ($this->config) {
            self::$actions['wp_enqueue_scripts'] = [[$this->config, 'hook']];
        }
    }
}
