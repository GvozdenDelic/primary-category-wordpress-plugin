document.addEventListener('DOMContentLoaded', () => {
    const primaryCategorySelect = document.querySelector('select[name="primary_category"]');
    const postType = primaryCategorySelect.dataset.postType;
    let categories = {};

    const loadCategories = (async () => {
        const categoriesURL = (postType == 'post') ? '../wp-json/wp/v2/categories' : `../wp-json/wp/v2/${postType}`;
        const categoriesResp = await fetch(categoriesURL);
        categories = await categoriesResp.json(); // Category ID is not awailable from DOM, so I had to fetch category data and store category IDs as values in primary category dropdown
    })();

    const updatePrimaryCategoryDropdown = (e) => {
        if(e.target.className == 'components-checkbox-control__input') {
            const categoryList = document.querySelectorAll('.editor-post-taxonomies__hierarchical-terms-list input');
            const selectedCategories = document.querySelectorAll('.editor-post-taxonomies__hierarchical-terms-list input:checked');
            primaryCategorySelect.innerHTML = '';
            console.log(categories);

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

    document.addEventListener('change', (e) => updatePrimaryCategoryDropdown(e)); 
});
