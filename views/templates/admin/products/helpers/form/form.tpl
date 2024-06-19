{block name="form_fields"}
    <div class="form-group">
        <label class="control-label col-lg-3" for="authors">
            {$smarty.const.LANG_AUTHORS}
        </label>
        <div class="col-lg-9">
            <div id="authors-list">
                {if isset($authors) && $authors|@count > 0}
                    {foreach from=$authors item=author}
                    <div class="author">
                        <input class="form-control" type="hidden" name="authors[]" value="{$author.id_author}">
                        <p>
                            <strong>{$author.first_name} {$author.last_name}</strong> -
                            <select class="form-select" name="contribution_types[]">
                                <option value="author" {if $author.contribution_type == 'author'}selected{/if}>Autore</option>
                                <option value="co-author" {if $author.contribution_type == 'co-author'}selected{/if}>Co-autore</option>
                                <option value="curator" {if $author.contribution_type == 'curator'}selected{/if}>Curatore</option>
                                <option value="editor" {if $author.contribution_type == 'editor'}selected{/if}>Editore</option>
                            </select>
                        </p>
                    </div>
                    {/foreach}
                {/if}
            </div>
            <div>
                <select class="form-control" id="add-author">
                    <option value="">Seleziona autore</option>
                    {if isset($all_authors) && $all_authors|@count > 0}
                        {foreach from=$all_authors item=all_author}
                        <option value="{$all_author.id_author}">{$all_author.first_name} {$all_author.last_name}</option>
                        {/foreach}
                    {/if}
                </select>
                <button type="button" class="btn btn-primary" onclick="addAuthor();">Aggiungi autore</button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function addAuthor() {
            var authorId = document.getElementById('add-author').value;
            var authorText = document.getElementById('add-author').options[document.getElementById('add-author').selectedIndex].text;
            if (authorId) {
                var newAuthor = document.createElement('div');
                newAuthor.classList.add('author');
                newAuthor.innerHTML = `
                    <input type="hidden" name="authors[]" value="` + authorId + `">
                    <p>
                        <strong>` + authorText + `</strong> -
                        <select class="form-select" name="contribution_types[]">
                            <option value="author">Autore</option>
                            <option value="co-author">Co-autore</option>
                            <option value="curator">Curatore</option>
                            <option value="editor">Editore</option>
                        </select>
                    </p>
                `;
                document.getElementById('authors-list').appendChild(newAuthor);
            }
        }
    </script>
{/block}
