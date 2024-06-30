
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
                LEFT JOIN ' . _DB_PREFIX_ . 'product_author pa ON p.id_product = pa.id_product
                WHERE pa.id_author = ' . (int)$id_author;
    $result = Db::getInstance()->executeS($sql);

    // Crea un array di prodotti
    $products = [];
    foreach ($result as $row) {
      $product = new Product((int)$row['id_product'], true, $this->context->language->id);
      if (Validate::isLoadedObject($product)) {
        $products[] = $this->prepareProductForTemplate($product);
      }
    }

    // Assegna i dati al template
    $this->context->smarty->assign([
      'author' => $author,
      'products' => $products,
      'listing' => ['products' => $products],
      'homeSize' => Image::getSize(ImageType::getFormattedName('home')),
    ]);

    // Imposta il template per la visualizzazione
    $this->setTemplate('module:authorsmanager/views/templates/front/author_details.tpl');
  }

  /**
   * Prepara i dati del prodotto per il template.
   *
   * @param Product $product
   * @return array
   */
  protected function prepareProductForTemplate($product)
  {
    // Recupera l'ID dell'immagine di copertina
    $cover_image = Product::getCover($product->id);
    $cover_image_id = isset($cover_image['id_image']) ? $cover_image['id_image'] : null;
    $image_link = $cover_image_id ? $this->context->link->getImageLink($product->link_rewrite, $cover_image_id, 'home_default') : null;

    return [
      'id_product' => $product->id,
      'name' => $product->name,
      'description_short' => $product->description_short,
      'price' => Tools::displayPrice($product->price),
      'link' => $this->context->link->getProductLink($product),
      'image' => $image_link,
      'id_product_attribute' => isset($product->id_product_attribute) ? $product->id_product_attribute : null,
      'cover' => $image_link,
      'url' => $this->context->link->getProductLink($product),
    ];
  }
}
