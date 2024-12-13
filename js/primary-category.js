document.addEventListener('DOMContentLoaded', () => {
    const primaryCategorySelect = document.querySelector('select[name="primary_category"]');
    const postType = primaryCategorySelect.dataset.postType;
    let categories = {};

    /* Category ID is not awailable from DOM, so I had to fetch category data and store category IDs as values in primary category dropdown */
    (async () => {
        const categoriesURL = (postType == 'post') ? '../wp-json/wp/v2/categories' : `../wp-json/wp/v2/${postType}`;
        const categoriesResp = await fetch(categoriesURL);

        /* Creating a const for categories here wouldn't work because of the block scope access of let/const. *
        * I have defined 'categories' variable in the outer scope, so it can be later used in 'updatePrimaryCategoryDropdown' */
        categories = await categoriesResp.json();
    })();

    const updatePrimaryCategoryDropdown = (e) => {
        if(e.target.className == 'components-checkbox-control__input') {
            const categoryList = document.querySelectorAll('.editor-post-taxonomies__hierarchical-terms-list input');
            const selectedCategories = document.querySelectorAll('.editor-post-taxonomies__hierarchical-terms-list input:checked');

            // On event category checkbox click/change, old <option> elements are deleted and new elements are appended to the <select> of primary category
            primaryCategorySelect.innerHTML = '';

            if(selectedCategories.length) {
                primaryCategorySelect.disabled = false;
                categoryList.forEach((category, i) => {
                    if(category.checked) {
                        const option = document.createElement('option');
                        option.value = (postType == 'post') ? categories[i].id : categories[0][i];
                        option.textContent = category.parentNode.nextSibling.innerHTML;
                        primaryCategorySelect.appendChild(option);
                    }
                });
            } else {
                primaryCategorySelect.disabled = true;
                const option = document.createElement('option');
                option.textContent = 'Please, select a category.';
                primaryCategorySelect.appendChild(option);
            }
        }
    }

    /* It would be the best to add event listeners only to category checkboxes. *
    * However, that doesn't work because category checkboxes don't exist yet on 'load' or 'DOMContentLoaded' event *
    * So I added an event listener to document and handled the event only if the event target is a category checkbox */
    document.addEventListener('change', (e) => updatePrimaryCategoryDropdown(e));
});
