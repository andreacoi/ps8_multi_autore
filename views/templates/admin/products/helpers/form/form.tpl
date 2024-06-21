<style>
img.top-logo {
    display:none;
  }
</style>

<div id="authors-manager">
    <div>
        <label for="author-select">{l s='Select Author' d='Modules.AuthorsManager'}</label>
        <select id="author-select">
            {foreach from=$authors item=author}
                <option value="{$author.id_author}">{$author.firstname} {$author.lastname}</option>
            {/foreach}
        </select>
        
        <label for="contribution-type-select">{l s='Contribution Type' d='Modules.AuthorsManager'}</label>
        <select id="contribution-type-select">
            <option value="author">{l s='Author' d='Modules.AuthorsManager'}</option>
            <option value="co-author">{l s='Co-Author' d='Modules.AuthorsManager'}</option>
            <option value="curator">{l s='Curator' d='Modules.AuthorsManager'}</option>
            <option value="editor">{l s='Editor' d='Modules.AuthorsManager'}</option>
        </select>
        
        <button type="button" id="add-author-btn">{l s='Add Author' d='Modules.AuthorsManager'}</button>
    </div>
    
    <div id="authors-list">
        {foreach from=$current_authors item=author}
            <div class="author-entry">
                <input type="hidden" name="authors[{$author.id_author}][id_author]" value="{$author.id_author}" />
                <input type="hidden" name="authors[{$author.id_author}][contribution_type]" value="{$author.contribution_type}" />
                <span>{$author.firstname} {$author.lastname} - 
                    <select name="authors[{$author.id_author}][contribution_type]">
                        <option value="author" {if $author.contribution_type == 'author'}selected{/if}>{l s='Author' d='Modules.AuthorsManager'}</option>
                        <option value="co-author" {if $author.contribution_type == 'co-author'}selected{/if}>{l s='Co-Author' d='Modules.AuthorsManager'}</option>
                        <option value="curator" {if $author.contribution_type == 'curator'}selected{/if}>{l s='Curator' d='Modules.AuthorsManager'}</option>
                        <option value="editor" {if $author.contribution_type == 'editor'}selected{/if}>{l s='Editor' d='Modules.AuthorsManager'}</option>
                    </select>
                </span>
                <button type="button" class="remove-author-btn">Remove</button>
            </div>
        {/foreach}
    </div>
</div>


<script type="text/javascript">
    

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('add-author-btn').addEventListener('click', function() {
        var authorSelect = document.getElementById('author-select');
        var id_author = authorSelect.value;
        var author_name = authorSelect.options[authorSelect.selectedIndex].text;

        var contributionTypeSelect = document.getElementById('contribution-type-select');
        var contribution_type = contributionTypeSelect.value;
        var contribution_type_text = contributionTypeSelect.options[contributionTypeSelect.selectedIndex].text;

        if (id_author && contribution_type) {
            var authorEntry = document.createElement('div');
            authorEntry.className = 'author-entry';

            var options = [
                '<option value="author"' + (contribution_type === 'author' ? ' selected' : '') + '>Author</option>',
                '<option value="co-author"' + (contribution_type === 'co-author' ? ' selected' : '') + '>Co-Author</option>',
                '<option value="curator"' + (contribution_type === 'curator' ? ' selected' : '') + '>Curator</option>',
                '<option value="editor"' + (contribution_type === 'editor' ? ' selected' : '') + '>Editor</option>',
            ].join('');

            authorEntry.innerHTML = `
                <input type="hidden" name="authors[`+ id_author +`][id_author]" value="`+ id_author +`" />
                <input type="hidden" name="authors[`+ id_author +`][contribution_type]" value="`+ contribution_type +`" />
                <span>`+ author_name +` 
                    <select name="authors[`+ id_author +`][contribution_type]">
                        `+ options +`
                    </select>
                </span>
                <button type="button" class="remove-author-btn">Remove</button>
            `;

            document.getElementById('authors-list').appendChild(authorEntry);
        }
    });

    document.getElementById('authors-list').addEventListener('click', function(event) {
        if (event.target && event.target.classList.contains('remove-author-btn')) {
            event.target.closest('.author-entry').remove();
        }
    });
});
</script>


