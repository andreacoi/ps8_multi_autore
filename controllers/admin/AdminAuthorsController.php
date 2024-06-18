
<?php

require_once _PS_MODULE_DIR_ . 'authorsmanager/authorsmanager.php';

class AdminAuthorsController extends ModuleAdminController
{
  public function __construct()
  {
    $this->bootstrap = true;
    $this->table = 'author';
    $this->className = 'Author';
    $this->lang = false;

    parent::__construct();

    $this->fields_list = array(
      'id_author' => array(
        'title' => $this->l('ID'),
        'align' => 'center',
        'class' => 'fixed-width-xs'
      ),
      'first_name' => array(
        'title' => $this->l('Nome')
      ),
      'last_name' => array(
        'title' => $this->l('Cognome')
      ),
      'biography' => array(
        'title' => $this->l('Biografia')
      ),
    );

    $this->bulk_actions = array('delete' => array('text' => $this->l('Cancella selezionati'), 'confirm' => $this->l('Sei sicuro di voler cancellare gli elementi selezionati?')));
  }

  public function renderForm()
  {
    $this->fields_form = array(
      'legend' => array(
        'title' => $this->l('Autore'),
        'icon' => 'icon-user'
      ),
      'input' => array(
        array(
          'type' => 'text',
          'label' => $this->l('Nome'),
          'name' => 'first_name',
          'required' => true,
        ),
        array(
          'type' => 'text',
          'label' => $this->l('Cognome'),
          'name' => 'last_name',
          'required' => true,
        ),
        array(
          'type' => 'textarea',
          'label' => $this->l('Biografia'),
          'name' => 'biography',
          'required' => false,
        ),
      ),
      'submit' => array(
        'title' => $this->l('Salva'),
      )
    );

    return parent::renderForm();
  }
}
