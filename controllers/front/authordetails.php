
<?php
class AuthorsManagerAuthorDetailsModuleFrontController extends ModuleFrontController
{
  public function initContent()
  {
    parent::initContent();

    // Recupera l'ID dell'autore dalla URL
    $id_author = (int)Tools::getValue('id_author');

    // Recupera i dettagli dell'autore dal database
    $author = Db::getInstance()->getRow('SELECT * FROM ' . _DB_PREFIX_ . 'author WHERE id_author = ' . $id_author);

    if (!$author) {
      Tools::redirect('index.php?controller=404');
    }

    // Recupera i libri associati all'autore
    $sql = 'SELECT p.id_product
                FROM ' . _DB_PREFIX_ . 'product p
                LEFT JOIN ' . _DB_PREFIX_ . 'product_author ab ON p.id_product = ab.id_product
                WHERE ab.id_author = ' . (int)$id_author;
    $result = Db::getInstance()->executeS($sql);

    // Crea un array di prodotti
    $products = [];
    foreach ($result as $row) {
      $products[] = (int)$row['id_product'];
    }

    // Recupera i dettagli dei prodotti
    $productsForTemplate = [];
    foreach ($products as $id_product) {
      $product = new Product($id_product, true, $this->context->language->id);
      if (Validate::isLoadedObject($product)) {
        $productsForTemplate[] = $product;
      }
    }

    // Assegna i dati al template
    $this->context->smarty->assign([
      'author' => $author,
      'products' => $productsForTemplate,
      'homeSize' => Image::getSize(ImageType::getFormattedName('home')),
    ]);

    // Imposta il template per la visualizzazione
    $this->setTemplate('module:authorsmanager/views/templates/front/author_details.tpl');
  }
}
