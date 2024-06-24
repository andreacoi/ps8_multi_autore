{extends file='page.tpl'}

{block name='page_content'}
    <div class="authors-list">
        <h1 class = "text-center mb-5" style="margin-bottom: 29px;">Autori</h1>
        <div class="container writerMenu">
          <a href="#A">A</a>
          <a href="#B">B</a>
          <a href="#C">C</a>
          <a href="#D">D</a>
          <a href="#E">E</a>
          <a href="#F">F</a>
          <a href="#G">G</a>
          <a href="#H">H</a>
          <a href="#I">I</a>
          <a href="#J">J</a>
          <a href="#K">K</a>
          <a href="#L">L</a>
          <a href="#M">M</a>
          <a href="#N">N</a>
          <a href="#O">O</a>
          <a href="#P">P</a>
          <a href="#Q">Q</a>
          <a href="#R">R</a>
          <a href="#S">S</a>
          <a href="#T">T</a>
          <a href="#U">U</a>
          <a href="#V">V</a>
          <a href="#W">W</a>
          <a href="#Y">Y</a>
          <a href="#Z">Z</a>
          <a href="#"></a>
        </div>
        <hr />
        <div class="container">
          {assign var="key" value=0}
          {foreach from=$letters item=$letter}
          {if $key != 0}
            <hr />
          {/if}
          <div class="row">
            <div class="col-md-3 col-xs-2">
              <h2 class="letter-index" id ="{$letter}">{$letter}</h2>
            </div>
            <div class="col-md-9 col-md-offset-0 col-xs-offset-1 col-xs-9">
              <ul class="writersList">
                {foreach from=$authors item=author}
                  {if $author.last_name|substr:0:1 == $letter}
                    <li><a href="{$link->getModuleLink('authorsmanager', 'authordetails', ['id_author' => $author.id_author])|escape:'html':'UTF-8'}">{$author.last_name}, {$author.first_name}</a></li>
                  {/if}
                {/foreach}
              </ul>
            </div>
          </div>
          {assign var=key value=$key+1}
          {/foreach}
        </div>
    </div>
{/block}
