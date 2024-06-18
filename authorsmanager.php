<?php
if (!defined('_PS_VERSION_')) {
  exit;
}

class AuthorsManager extends Module
{
  public function __construct()
  {
    $this->name = 'authorsmanager';
    $this->version = '1.0.0';
    $this->author = 'Andrea Coi';
    $this->tab = 'administration';
    $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    $this->bootstrap = true;

    parent::__construct();

    $this->displayName = $this->l('Gestisci autori');
    $this->description = $this->l('Modulo per la gestione di autori multipli.');
  }

  public function install()
  {
    return parent::install() &&
      $this->registerHook('displayAdminProductsExtra') &&
      $this->installTab();
  }

  public function uninstall()
  {
    return parent::uninstall() && $this->uninstallTab();
  }

  private function installTab()
  {
    $tab = new Tab();
    $tab->active = 1;
    $tab->class_name = 'AdminAuthors';
    $tab->name = array();
    foreach (Language::getLanguages(true) as $lang) {
      $tab->name[$lang['id_lang']] = 'Authors';
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

  public function hookDisplayAdminProductsExtra($params)
  {
    $id_product = (int)$params['id_product'];
    $authors = $this->getProductAuthors($id_product);
    $all_authors = $this->getAllAuthors();

    $this->context->smarty->assign(array(
      'authors' => $authors,
      'all_authors' => $all_authors,
      'product_id' => $id_product,
    ));

    return $this->display(__FILE__, 'views/templates/admin/authors.tpl');
  }

  private function getProductAuthors($id_product)
  {
    $sql = new DbQuery();
    $sql->select('a.id_author, a.first_name, a.last_name, pa.contribution_type')
      ->from('product_author', 'pa')
      ->leftJoin('author', 'a', 'pa.id_author = a.id_author')
      ->where('pa.id_product = ' . (int)$id_product);

    return Db::getInstance()->executeS($sql);
  }

  private function getAllAuthors()
  {
    $sql = new DbQuery();
    $sql->select('a.id_author, a.first_name, a.last_name')
      ->from('author', 'a');

    return Db::getInstance()->executeS($sql);
  }
}
