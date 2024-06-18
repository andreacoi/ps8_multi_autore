
<?php
class AdminProductsController extends AdminProductsControllerCore
{
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

  private function saveProductAuthors()
  {
    $id_product = (int)Tools::getValue('id_product');
    Db::getInstance()->delete('product_author', 'id_product = ' . $id_product);

    $authors = Tools::getValue('authors');
    $contribution_types = Tools::getValue('contribution_types');

    if ($authors && is_array($authors)) {
      foreach ($authors as $key => $id_author) {
        if ($id_author) {
          $contribution_type = pSQL($contribution_types[$key]);
          Db::getInstance()->insert('product_author', array(
            'id_product' => $id_product,
            'id_author' => (int)$id_author,
            'contribution_type' => $contribution_type,
          ));
        }
      }
    }
  }
}
