<?php

namespace Jascha030\WP\Ajax\Script;

class AjaxScriptConfig
{
    private $name;

    private $src;

    private $variable;

    private $enqueue;

    public function __construct(string $name, string $src, string $variable = null, bool $enqueue = true)
    {
        $this->name = $name;

        $this->src = $src;

        $this->variable = $variable ?? 'plugin_ajax';

        $this->enqueue = $enqueue;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSrc(): string
    {
        return $this->src;
    }

    /**
     * @return string
     */
    public function getVariable(): string
    {
        return $this->variable;
    }

    /**
     * @return bool
     */
    public function doEnqueue(): bool
    {
        return $this->enqueue;
    }

    public function hook()
    {
        wp_register_script($this->config->getName(), $this->config->getSrc(), ['jquery']);

        wp_localize_script($this->config->getName(), $this->config->getVariable(), [
            'ajax_url' => admin_url('admin_ajax.php')
        ]);

        if ($this->config->doEnqueue()) {
            wp_enqueue_script($this->config->getName());
        }
    }
}
