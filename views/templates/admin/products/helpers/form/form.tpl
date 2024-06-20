
<style>
img.top-logo {
    display:none;
}
</style>
<div class="panel">
    <h3 class="mt-3 mb-2">{l s='Autori' d='Modules.AuthorsManager.Admin'}</h3>
    <div class="form-group form-inline mx-4">
        <label class="mr-3" for="author">{l s='Aggiungi autori al libro' d='Modules.AuthorsManager.Admin'}</label>
        <select id="add-author" class="form-control ml-2">
            <option value="">{l s='Seleziona autore tra quelli presenti' d='Modules.AuthorsManager.Admin'}</option>
            {foreach from=$all_authors item=all_author}
                _<option value="{$all_author.id_author}">{$all_author.first_name} {$all_author.last_name}</option>
            {/foreach}
        </select>
        <button type="button" class="btn btn-primary" id="add-author-btn">{l s='Aggiungi' d='Modules.AuthorsManager.Admin'}</button>
    </div>
    <h3 class="mt-3 mb-2">Lista degli autori di questo libro</h3>
    <div id="authors-list ml-4">
        {foreach from=$authors item=author}
            <div class="author-item" data-id="{$author.id_author}">
                <input type="hidden" name="authors[]" value="{$author.id_author}" />
                <input type="hidden" name="contribution_types[]" value="{$author.contribution_type}" />
                <p>
                    <div class="form-group form-inline mx-4">
                      <strong class="mr-3">{$author.first_name} {$author.last_name}</strong> 
                      <select class="form-control" name="contribution_types[]">
                          <option value="author" {if $author.contribution_type == 'author'}selected{/if}>{l s='Autore' d='Modules.AuthorsManager.Admin'}</option>
                          <option value="co-author" {if $author.contribution_type == 'co-author'}selected{/if}>{l s='Co-Autore' d='Modules.AuthorsManager.Admin'}</option>
                          <option value="curator" {if $author.contribution_type == 'curator'}selected{/if}>{l s='Curatore' d='Modules.AuthorsManager.Admin'}</option>
                          <option value="editor" {if $author.contribution_type == 'editor'}selected{/if}>{l s='Editore' d='Modules.AuthorsManager.Admin'}</option>
                      </select>
                      <button type="button" class="btn btn-danger remove-author">{l s='Rimuovi' d='Modules.AuthorsManager.Admin'}</button>
                    </div>
                </p>
            </div>
        {/foreach}
    </div>
</div>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('add-author-btn').addEventListener('click', function() {
            var authorSelect = document.getElementById('add-author');
            var authorId = authorSelect.value;
            var authorText = authorSelect.options[authorSelect.selectedIndex].text;

            if (authorId) {
                var newAuthor = document.createElement('div');
                newAuthor.classList.add('author-item');
                newAuthor.setAttribute('data-id', authorId);
                newAuthor.innerHTML = `
                    <input type="hidden" name="authors[]" value="`+ authorId +`" />
                    <input type="hidden" name="contribution_types[]" value="author" />
                    <p>
                        <strong>` + authorText + ` </strong> - 
                        <select class="form-control" name="contribution_types[]">
                            <option value="author">{l s='Autore' d='Modules.AuthorsManager.Admin'}</option>
                            <option value="co-author">{l s='Co-Autore' d='Modules.AuthorsManager.Admin'}</option>
                            <option value="curator">{l s='Curatore' d='Modules.AuthorsManager.Admin'}</option>
                            <option value="editor">{l s='Editore' d='Modules.AuthorsManager.Admin'}</option>
                        </select>
                        <button type="button" class="btn btn-danger remove-author">{l s='Rimuovi autore' d='Modules.AuthorsManager.Admin'}</button>
                    </p>
                `;
                document.getElementById('authors-list').appendChild(newAuthor);

                // Reset select
                authorSelect.value = '';
            }
        });

        document.getElementById('authors-list').addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-author')) {
                var authorItem = event.target.closest('.author-item');
                var authorId = authorItem.getAttribute('data-id');

                // Add hidden input to mark author for removal
                var removeInput = document.createElement('input');
                removeInput.type = 'hidden';
                removeInput.name = 'remove_authors[]';
                removeInput.value = authorId;
                document.getElementById('authors-list').appendChild(removeInput);

                // Remove the author item from the list
                authorItem.remove();
            }
        });
    });
</script>

