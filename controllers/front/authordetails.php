
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
        $products[] = $this->getProductData($product);
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
   * Costruisce manualmente i dati del prodotto.
   *
   * @param Product $product
   * @return array
   */


  protected function getProductData(Product $product)
  {
    $link = $this->context->link;
    $cover = Product::getCover($product->id);

    $image = null;
    if ($cover) {
      $imageUrl = $link->getImageLink($product->link_rewrite, $product->id . '-' . $cover['id_image'], 'home_default');
      $image = [
        'bySize' => [
          'home_default' => [
            'url' => $imageUrl,
            'width' => 250, // esempio di larghezza
            'height' => 250 // esempio di altezza
          ],
          'large' => [
            'url' => $link->getImageLink($product->link_rewrite, $product->id . '-' . $cover['id_image'], 'large_default')
          ]
        ],
        'legend' => $product->name
      ];
    }

    // Otteniamo il prezzo formattato correttamente
    $price = Tools::displayPrice($product->getPrice(true, null, 2), $this->context->currency);

    return [
      'id_product' => $product->id,
      'name' => $product->name,
      'description_short' => $product->description_short,
      'price' => $price,
      'link_rewrite' => $product->link_rewrite,
      'category' => $product->category,
      'id_image' => $cover ? $cover['id_image'] : null,
      'url' => $link->getProductLink($product),
      'cover' => $image,
      'show_price' => true,
      'has_discount' => $product->has_discount,
      'regular_price' => Tools::displayPrice($product->getPriceWithoutReduct(), $this->context->currency),
      'discount_percentage' => $product->specificPrice['reduction'] * 100 . '%',
      'discount_amount_to_display' => Tools::displayPrice($product->getPriceWithoutReduct() - $product->getPrice(), $this->context->currency),
      'discount_type' => $product->specificPrice['reduction_type'],
    ];
  }
}
