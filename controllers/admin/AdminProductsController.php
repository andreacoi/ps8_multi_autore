
<?php

require_once _PS_MODULE_DIR_ . 'authorsmanager/classes/Author.php';

class AdminProductsController extends AdminProductsControllerCore
{
  public function __construct()
  {
    parent::__construct();
    $this->bootstrap = true;
  }

  public function renderForm()
  {
    // Carica gli autori associati al prodotto
    $id_product = (int)Tools::getValue('id_product');
    $authors = [];
    if ($id_product) {
      $authors = Db::getInstance()->executeS(
        '
                SELECT pa.id_author, a.first_name, a.last_name, pa.contribution_type
                FROM ' . _DB_PREFIX_ . 'product_author pa
                LEFT JOIN ' . _DB_PREFIX_ . 'author a ON pa.id_author = a.id_author
                WHERE pa.id_product = ' . (int)$id_product
      );
    }

    // Carica tutti gli autori per il dropdown
    $all_authors = Db::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'author');

    $this->context->smarty->assign([
      'authors' => $authors,
      'all_authors' => $all_authors,
    ]);

    // Aggiungi il codice per gestire gli autori nel template del prodotto
    $this->fields_form_override[] = [
      'type' => 'authors',
      'label' => $this->l('Authors'),
      'name' => 'authors',
    ];

    return parent::renderForm();
  }

  public function processAdd()
  {
    parent::processAdd();
    $this->saveProductAuthors();
  }

  public function processUpdate()
  {
    parent::processUpdate();
    $this->saveProductAuthors();
  }

  protected function saveProductAuthors()
  {
    $id_product = (int)Tools::getValue('id_product');
    Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'product_author WHERE id_product = ' . (int)$id_product);

    $authors = Tools::getValue('authors', []);
    $contribution_types = Tools::getValue('contribution_types', []);

    if (!empty($authors)) {
      foreach ($authors as $key => $id_author) {
        Db::getInstance()->insert('product_author', [
          'id_product' => (int)$id_product,
          'id_author' => (int)$id_author,
          'contribution_type' => pSQL($contribution_types[$key]),
        ]);
      }
    }
  }
}
