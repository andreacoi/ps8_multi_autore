<?php
class AuthorsManagerAuthorsListModuleFrontController extends ModuleFrontController
{
  public function initContent()
  {
    parent::initContent();

    // Recupera la lista di tutti gli autori
    $authors = $this->getAllAuthors();
    $letters = [
      'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
      'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
    ];

    $this->context->smarty->assign([
      'authors' => $authors,
      'letters' => $letters,
    ]);

    $this->setTemplate('module:authorsmanager/views/templates/front/authors_list.tpl');
  }

  private function getAllAuthors()
  {
    $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'author ORDER BY last_name ASC';
    return Db::getInstance()->executeS($sql);
  }
}
