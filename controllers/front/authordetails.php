
<?php
class AuthorsManagerAuthorDetailsModuleFrontController extends ModuleFrontController
{
  public function initContent()
  {
    parent::initContent();

    // Recupera l'ID dell'autore dalla richiesta
    $id_author = (int)Tools::getValue('id_author');
    $author = $this->getAuthorById($id_author);
    $books = $this->getBooksByAuthorId($id_author);

    $this->context->smarty->assign([
      'author' => $author,
      'books' => $books,
    ]);

    $this->setTemplate('module:authorsmanager/views/templates/front/author_details.tpl');
  }

  private function getAuthorById($id_author)
  {
    $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'author WHERE id_author = ' . (int)$id_author;
    return Db::getInstance()->getRow($sql);
  }

  private function getBooksByAuthorId($id_author)
  {
    $sql = 'SELECT p.id_product, p.name 
                FROM ' . _DB_PREFIX_ . 'product p
                INNER JOIN ' . _DB_PREFIX_ . 'product_author pa ON p.id_product = pa.id_product
                WHERE pa.id_author = ' . (int)$id_author;
    return Db::getInstance()->executeS($sql);
  }
}
