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
    $this->version = '0.0.1.5-pre-alpha';
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
      $this->registerHook('actionAdminProductsControllerSaveAfter') &&
      $this->createTables() &&
      $this->installTab();
  }

  public function uninstall()
  {
    return parent::uninstall() && $this->deleteTables() && $this->uninstallTab();
  }

  protected function createTables()
  {
    $sql = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "author` (
            `id_author` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `first_name` VARCHAR(255) NOT NULL,
            `last_name` VARCHAR(255) NOT NULL,
            `biography` TEXT,
            PRIMARY KEY (`id_author`)
        ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";

    $sql .= "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "product_author` (
            `id_product` INT UNSIGNED NOT NULL,
            `id_author` INT UNSIGNED NOT NULL,
            `contribution_type` VARCHAR(255) NOT NULL,
            PRIMARY KEY (`id_product`, `id_author`),
            CONSTRAINT `fk_product_author_product` FOREIGN KEY (`id_product`) REFERENCES `" . _DB_PREFIX_ . "product` (`id_product`) ON DELETE CASCADE,
            CONSTRAINT `fk_product_author_author` FOREIGN KEY (`id_author`) REFERENCES `" . _DB_PREFIX_ . "author` (`id_author`) ON DELETE CASCADE
        ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";

    return Db::getInstance()->execute($sql);
  }

  protected function deleteTables()
  {
    $sql = "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "product_author`;";
    $sql .= "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "author`;";
    return Db::getInstance()->execute($sql);
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

  public function hookActionAdminProductsControllerSaveAfter($params)
  {
    $id_product = (int)$params['id_product'];
    $authors = Tools::getValue('authors', []);
    $contribution_types = Tools::getValue('contribution_types', []);

    Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'product_author WHERE id_product = ' . (int)$id_product);

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
