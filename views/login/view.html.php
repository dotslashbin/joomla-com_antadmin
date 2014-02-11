<?php

    defined('_JEXEC') or die;

    class AntadminViewLogin extends JViewLegacy
    {
            protected $form;

            protected $params;

            protected $state;

            protected $user;

            /**
             * Method to display the view.
             *
             * @param   string	The template file to include
             * @since   1.5
             */
            public function display( $tpl = null )
            {
                parent::display($tpl);
            }
    }
