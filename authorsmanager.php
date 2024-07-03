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
      $this->registerHook('moduleRoutes') &&
      $this->registerHook('header') &&
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
    /* N.B. Gli editor (scritto rigorosamente in inglese) sono la stessa cosa dei curatori.
    * Questo perché fino a un certo momento storico, nell'editoria italiana si è usata la locuzione "a cura di" per poi essere sostituita da "ed."
    * C'è però un problema: mentre con la locuzione "a cura di" ci si occupa semplicemente di posporre la locuzione alla fine della lista degli autori, per la nuova dicitura, quella internazionale va indicato anche il plurale, reso come "Edd."
    * Queste variabili si occupano del conteggio_editor e del conteggio_curator.
    * Il conteggio_editor conta quanti autori ci sono marcati come editor, quello curator conta quanti autori ci sono come curator.
    * Questo conteggio è utile se, e solo se, il numero di autori è DIVERSO DA 1.
    * Questo perché la dicitura riguardante la contribuzione va messa alla fine se gli autori sono più di uno, viceversa se l'autore è unico la dicitura va messa a fianco al nome.
    * Per fare ciò inizializzero la variabile "contribuzione" che assumerà valore diverso da Null se gli autori sono più di uno.
    * */
    if (count($authors) == 1) {
      switch ($authors[0]['contribution_type']) {
        case 'editor':
          $contribuzione = "(ed.)";
          break;
        case 'curator':
          $contribuzione = "(a cura di)";
          break;
        default:
          $contribuzione = "";
          break;
      }
    } else if (count($authors) > 1) {
      switch ($authors[0]['contribution_type']) {
        case 'editor':
          $contribuzione = "(edd.)";
          break;
        case 'curator':
          $contribuzione = "(a cura di)";
          break;
        default:
          $contribuzione = "";
          break;
      }
    }

    $this->context->smarty->assign([
      'contribuzione' => $contribuzione,
      'authors' => $authors,
    ]);

    return $this->display(__FILE__, 'views/templates/hook/product_authors.tpl');
  }

  public function hookActionProductSave($params)
  {
    $id_product = (int)$params['product']->id;
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
  // seguo https://devdocs.prestashop-project.org/8/modules/concepts/hooks/list-of-hooks/moduleroutes/
  public function hookModuleRoutes($params)
  {
    return [
      'module-authorsmanager-authorslist' => [
        'rule' => 'autori',
        'keywords' => [],
        'controller' => 'authorslist',
        'params' => [
          'fc' => 'module',
          'module' => 'authorsmanager'
        ]
      ],
      'module-authorsmanager-authorsdetails' => [
        'rule' => 'autore/{id_author}',
        'keywords' => [
          'id_author' => [
            'regexp' => '[0-9]*',
            'param' => 'id_author'
          ],
        ],
        'controller' => 'authordetails',
        'params' => [
          'fc' => 'module',
          'module' => 'authorsmanager'
        ]
      ],

    ];
  }

  public function hookHeader($params)
  {
    // Aggiungi il file CSS personalizzato
    $this->context->controller->registerStylesheet(
      'authorsmanager-css',
      'modules/' . $this->name . '/views/css/authorsmanager.css',
      ['media' => 'all', 'priority' => 150]
    );
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

  protected function isMultipleEditor($id_product)
  {
    if (!$id_product) {
      return false;
    }
    $sql = '
            SELECT pa.id_author, a.first_name, a.last_name, pa.contribution_type
            FROM ' . _DB_PREFIX_ . 'product_author pa
            LEFT JOIN ' . _DB_PREFIX_ . 'author a ON pa.id_author = a.id_author
      WHERE pa.id_product = ' . (int)$id_product . ' AND pa.contribution_type = "editor";';
    return Db::getInstance()->executeS($sql);
  }

  protected function isMultipleCurator($id_product)
  {
    if (!$id_product) {
      return false;
    }
    $sql = '
            SELECT pa.id_author, a.first_name, a.last_name, pa.contribution_type
            FROM ' . _DB_PREFIX_ . 'product_author pa
            LEFT JOIN ' . _DB_PREFIX_ . 'author a ON pa.id_author = a.id_author
      WHERE pa.id_product = ' . (int)$id_product . ' AND pa.contribution_type = "curator";';
    return Db::getInstance()->executeS($sql);
  }


  protected function getAllAuthors()
  {
    $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'author ORDER BY last_name ASC';
    return Db::getInstance()->executeS($sql);
  }
}
