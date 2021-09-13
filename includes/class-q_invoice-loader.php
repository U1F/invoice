<?php
/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * PHP version 5
 * 
 * @category   Class
 * @package    Q_Invoice
 * @subpackage Q_Invoice/Includes
 * @author     qanuk.io <support@qanuk.io>
 * @license    License example.org
 * @link       https://www.qanuk.io/
 * @since      1.0.0
 */

if (!class_exists('QI_Q_Invoice_Loader')) {
    /**
     * Class QI_Q_Invoice_Loader
     * 
     * @category   Class/Loader
     * @package    Q_Invoice
     * @subpackage Q_Invoice/Includes
     * @author     qanuk.io <support@qanuk.io>
     * @license    License example.org
     * @link       https://www.qanuk.io/
     */
    class QI_Q_Invoice_Loader
    {

        /**
         * The array of actions registered with WordPress.
         *
         * @since  1.0.0
         * @access protected
         * @var    array    $actions    The actions registered with WordPress 
         *                              to fire when the plugin loads.
         */
        protected $actions;

        /**
         * The array of filters registered with WordPress.
         *
         * @since  1.0.0
         * @access protected
         * @var    array    $filters    The filters registered with WordPress 
         *                              to fire when the plugin loads.
         */
        protected $filters;

        /**
         * Initialize the collections used to maintain the actions and filters.
         *
         * @since 1.0.0
         */
        public function __construct()
        {
            $this->actions = array();
            $this->filters = array();
        }

        /**
         * Add a new action to the collection to be registered with WordPress.
         *
         * @param string $hook          The name of the WordPress action registered.
         * @param object $component     Reference to the instance 
         *                              the action is defined on.
         * @param string $callback      The name of the function definition 
         *                              on the $component.
         * @param int    $priority      Optional. The priority at which the function 
         *                              should be fired. Default is 10.
         * @param int    $accepted_args Optional. The number of arguments that 
         *                              should be passed to the $callback. Default=1.
         * 
         * @return void 
         * 
         * @since 1.0.0
         */
        public function addAction(
            $hook, 
            $component, 
            $callback, 
            $priority = 10, 
            $accepted_args = 1
        ) {
            $this->actions = $this->_add(
                $this->actions, 
                $hook, 
                $component, 
                $callback, 
                $priority, 
                $accepted_args
            );
        }

        /**
         * Add a new filter to the collection to be registered with WordPress.
         *
         * @param string $hook          The name of the WordPress filter 
         *                              that is being registered.
         * @param object $component     A reference to the instance of the object 
         *                              on which the filter is defined.
         * @param string $callback      The name of the function 
         *                              definition on the $component.
         * @param int    $priority      Optional. The priority at which 
         *                              the function should be fired. Default is 10.
         * @param int    $accepted_args Optional. The number of arguments that 
         *                              should be passed to the $callback. Default=1
         * 
         * @return void
         * 
         * @since 1.0.0
         */
        public function addFilter(
            $hook, 
            $component, 
            $callback, 
            $priority = 10, 
            $accepted_args = 1
        ) {
            $this->filters = $this->_add(
                $this->filters, 
                $hook, 
                $component, 
                $callback, 
                $priority, 
                $accepted_args
            );
        }

        /**
         * A utility function that is used to register 
         * the actions and hooks into a single collection.
         *
         * @param array  $hooks         The collection of hooks that are registered 
         *                              (actions or filters).
         * @param string $hook          The name of the WordPress filter registered.
         * @param object $component     A reference to the instance of the object 
         *                              on which the filter is defined.
         * @param string $callback      The name of the function definition 
         *                              on the $component.
         * @param int    $priority      The priority at which the function 
         *                              should be fired.
         * @param int    $accepted_args The number of arguments that 
         *                              should be passed to the $callback.
         *                                   
         * @return array The collection of actions and filters 
         *               registered with WordPress.
         * 
         * @access private 
         * 
         * @since 1.0.0
         */
        private function _add(
            $hooks, 
            $hook, 
            $component, 
            $callback, 
            $priority, 
            $accepted_args
        ) {
            $hooks[] = array(
                'hook'          => $hook,
                'component'     => $component,
                'callback'      => $callback,
                'priority'      => $priority,
                'accepted_args' => $accepted_args
            );

            return $hooks;
        }

        /**
         * Register the filters and actions with WordPress.
         *
         * @since 1.0.0
         * 
         * @return void
         */
        public function run()
        {

            foreach ($this->filters as $hook) {
                add_filter(
                    $hook['hook'], 
                    array($hook['component'], 
                    $hook['callback']), 
                    $hook['priority'], 
                    $hook['accepted_args']
                );
            }

            foreach ($this->actions as $hook) {
                add_action(
                    $hook['hook'], 
                    array($hook['component'], 
                    $hook['callback']), 
                    $hook['priority'], 
                    $hook['accepted_args']
                );
            }
        }
    }
}
