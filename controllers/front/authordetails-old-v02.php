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
        $products[] = $this->getProductAsArray($product);
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
   * Converte un oggetto Product in un array utilizzando ProductAssembler.
   *
   * @param Product $product
   * @return array
   */
  protected function getProductAsArray(Product $product)
  {
    $assembler = new PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductAssembler($this->context);
    $presenter = new PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductPresenter(
      $this->context->link,
      $this->context->getTranslator(),
      new PrestaShop\PrestaShop\Adapter\Image\ImageRetriever($this->context->link),
      new PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever(),
      $this->context->getTranslator()
    );

    $productData = $assembler->assembleProduct($product);
    $settings = $this->getProductPresentationSettings();

    return $presenter->present($settings, $productData, $this->context->language);
  }

  /**
   * Imposta i dati del prodotto per la presentazione.
   *
   * @return ProductPresentationSettings
   */
  protected function getProductPresentationSettings()
  {
    $settings = new PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductPresentationSettings();
    $settings->catalog_mode = (bool)Configuration::get('PS_CATALOG_MODE');
    $settings->include_taxes = !Product::getTaxCalculationMethod();
    $settings->specific_price = null;
    $settings->ecotax_rate = Configuration::get('PS_ECOTAX_TAX_RATE');
    $settings->customer_group_id = (int)Group::getCurrent()->id;
    $settings->add_prod_display = Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY');
    return $settings;
  }
}
