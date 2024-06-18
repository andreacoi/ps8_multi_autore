<div class="panel">
    <h3>{$smarty.const.LANG_AUTHORS}</h3>
    <div id="authors-list">
        {foreach from=$authors item=author}
            <div class="author">
                <input type="hidden" name="authors[]" value="{$author.id_author}">
                <p>
                    <strong>{$author.first_name} {$author.last_name}</strong> - 
                    <select name="contribution_types[]">
                        <option value="author" {if $author.contribution_type == 'author'}selected{/if}>Author</option>
                        <option value="co-author" {if $author.contribution_type == 'co-author'}selected{/if}>Co-Author</option>
                        <option value="curator" {if $author.contribution_type == 'curator'}selected{/if}>Curator</option>
                        <option value="editor" {if $author.contribution_type == 'editor'}selected{/if}>Editor</option>
                    </select>
                </p>
            </div>
        {/foreach}
    </div>
    <div>
        <select id="add-author">
            <option value="">{$smarty.const.LANG_SELECT_AUTHOR}</option>
            {foreach from=$all_authors item=all_author}
                <option value="{$all_author.id_author}">{$all_author.first_name} {$all_author.last_name}</option>
            {/foreach}
        </select>
        <button type="button" class="btn btn-primary" onclick="addAuthor();">{$smarty.const.LANG_ADD_AUTHOR}</button>
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
                    <select name="contribution_types[]">
                        <option value="author">Author</option>
                        <option value="co-author">Co-Author</option>
                        <option value="curator">Curator</option>
                        <option value="editor">Editor</option>
                    </select>
                </p>
            `;
            document.getElementById('authors-list').appendChild(newAuthor);
        }
    }
</script>
