<?php
class AuthorsManagerAuthorsListModuleFrontController extends ModuleFrontController
{
  public function initContent()
  {
    parent::initContent();

    // Recupera la lista di tutti gli autori
    $authors = $this->getAllAuthors();

    $this->context->smarty->assign([
      'authors' => $authors,
    ]);

    $this->setTemplate('module:authorsmanager/views/templates/front/authors_list.tpl');
  }

  private function getAllAuthors()
  {
    $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'author';
    return Db::getInstance()->executeS($sql);
  }
}
