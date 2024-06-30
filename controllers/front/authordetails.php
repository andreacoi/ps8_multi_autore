
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
        $product_data = array(
          'id_product' => $product->id,
          'name' => $product->name,
          'price' => $product->getPrice(true, null, 2),
          'cover_image' => isset($coverImage['bySize']['home_default']['url']) ? $coverImage['bySize']['home_default']['url'] : '', // Verifica se la chiave 'url' esiste prima di accedere ad essa
          // Puoi aggiungere altri dati del prodotto qui, se necessario
        );
        // Assicurati che l'URL dell'immagine di copertina sia valido prima di passarlo al template
        if (!empty($coverImage['bySize']['home_default']['url'])) {
          $product_data['cover_image'] = $coverImage['bySize']['home_default']['url'];
        } else {
          $product_data['cover_image'] = _PS_IMG_DIR_ . 'p/' . Tools::strtolower($product->id) . '-' . Tools::strtolower(Product::getCover($product->id)['id_image']) . '.jpg';
        }
        // Se il prodotto ha attributi, aggiungi anche l'id_product_attribute
        if ($product->hasAttributes()) {
          $product_data['id_product_attribute'] = $product->id_default_attribute;
        } else {
          $product_data['id_product_attribute'] = 0; // Assegna un valore di default se non ci sono attributi
        }
        $products[] = $product_data;
        // Calcolo delle variabili di paginazione
        $total_products = count($products);
        $from = 1; // Posizione di partenza
        $to = $total_products; // Posizione finale
        $total = $total_products; // Totale degli elementi
      }
    }

    // Assegna i dati al template
    $this->context->smarty->assign([
      'total_products' => $total_products,
      'author' => $author,
      'products' => $products,
      'listing' => [
        'products' => $products,
        'pagination' => array(
          'items_shown_from' => $from,
          'items_shown_to' => $to,
          'total_items' => $total,
        ),
      ],
      'homeSize' => Image::getSize(ImageType::getFormattedName('home')),
    ]);

    // Imposta il template per la visualizzazione
    $this->setTemplate('module:authorsmanager/views/templates/front/author_details.tpl');
  }
}
