<?php

namespace Jascha030\WP\Ajax\Script;

use Jascha030\WP\Utilities\Script\ScriptFile;

if (! class_exists('Jascha030\WP\Ajax\Script\AjaxScriptFile')) {
    /**
     * Class AjaxScriptFile
     *
     * @package Jascha030\WP\Ajax\Script
     */
    class AjaxScriptFile extends ScriptFile
    {
        /**
         * @var string|null Javascript global
         */
        private $variable;

        /**
         * AjaxScriptConfig constructor.
         *
         * @param string $handle
         * @param string $src
         * @param string|null $variable
         */
        public function __construct(string $handle, string $src, string $variable = null)
        {
            parent::__construct($handle, $src, null);

            $this->variable = $variable ?? 'plugin_ajax';

            $this->localize();
        }

        /**
         * @return string
         */
        public function getVariable(): string
        {
            return $this->variable;
        }

        /**
         * Localize script with wordpress and set javascript global
         */
        private function localize()
        {
            wp_localize_script($this->getHandle(), $this->getVariable(), [
                'ajax_url' => admin_url('admin_ajax.php')
            ]);
        }
    }
}
