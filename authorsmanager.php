<?php
if (!defined('_PS_VERSION_')) {
  exit;
}

class AuthorsManager extends Module
{
  public function __construct()
  {
    $this->name = 'authorsmanager';
    $this->tab = 'administration';
    $this->version = '0.0.2.2.1pa';
    $this->author = 'Andrea Coi';
    $this->need_instance = 0;

    parent::__construct();

    $this->displayName = $this->l('Gestisci autori');
    $this->description = $this->l('Modulo per gestire gli autori multipli per ogni libro.');
  }
  private function installTab()
  {
    $tab = new Tab();
    $tab->active = 1;
    $tab->class_name = 'AdminAuthors';
    $tab->name = array();
    foreach (Language::getLanguages(true) as $lang) {
      $tab->name[$lang['id_lang']] = 'Autori';
    }
    $tab->id_parent = (int)Tab::getIdFromClassName('AdminCatalog');
    $tab->module = $this->name;

    return $tab->add();
  }

  private function uninstallTab()
  {
    $id_tab = (int)Tab::getIdFromClassName('AdminAuthors');
    if ($id_tab) {
      $tab = new Tab($id_tab);
      return $tab->delete();
    }
    return false;
  }
  public function install()
  {
    return parent::install() &&
      $this->registerHook('displayAdminProductsExtra') &&
      $this->registerHook('actionProductSave') &&
      $this->registerHook('displayProductAuthors') &&
      $this->installTab();
  }

  public function uninstall()
  {
    return parent::uninstall() && $this->uninstallTab();
  }

  public function hookDisplayAdminProductsExtra($params)
  {
    $id_product = (int)$params['id_product'];
    $authors = $this->getProductAuthors($id_product);
    $all_authors = $this->getAllAuthors();

    $this->context->smarty->assign([
      'authors' => $authors,
      'all_authors' => $all_authors,
    ]);

    return $this->display(__FILE__, 'views/templates/admin/products/helpers/form/form.tpl');
  }

  public function hookDisplayProductAuthors($params)
  {
    $id_product = (int)$params['product']['id_product'];
    $authors = $this->getProductAuthors($id_product);

    $this->context->smarty->assign([
      'authors' => $authors,
    ]);

    return $this->display(__FILE__, 'views/templates/hook/product_authors.tpl');
  }

  public function hookActionProductSave($params)
  {
    $id_product = (int)$params['object']->id;
    $authors = Tools::getValue('authors'); // Supponiamo che 'authors' sia l'array contenente gli autori e i loro tipi di contribuzione

    if (is_array($authors) && !empty($authors)) {
      // Cancella le associazioni esistenti
      Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'product_author WHERE id_product = ' . (int)$id_product);

      // Inserisci le nuove associazioni
      foreach ($authors as $author) {
        $id_author = (int)$author['id_author'];
        $contribution_type = pSQL($author['contribution_type']); // 'author', 'co-author', 'curator', 'editor'

        Db::getInstance()->insert('product_author', [
          'id_product' => (int)$id_product,
          'id_author' => (int)$id_author,
          'contribution_type' => pSQL($contribution_type),
        ]);
      }
    }
  }
  protected function getProductAuthors($id_product)
  {
    if (!$id_product) {
      return [];
    }

    $sql = '
            SELECT pa.id_author, a.first_name, a.last_name, pa.contribution_type
            FROM ' . _DB_PREFIX_ . 'product_author pa
            LEFT JOIN ' . _DB_PREFIX_ . 'author a ON pa.id_author = a.id_author
            WHERE pa.id_product = ' . (int)$id_product;

    return Db::getInstance()->executeS($sql);
  }

  protected function getAllAuthors()
  {
    $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'author';
    return Db::getInstance()->executeS($sql);
  }
}
