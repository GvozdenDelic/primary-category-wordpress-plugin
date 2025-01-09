This Wordpress plugin is intended to add "Primary Category" feature on category posts and custom posts.

Primary Category plugin time log:

2h - Planning

Reading the requirements and creating a plan. In this phase I make sure that I have understood the requirements and if needed, ask questions to further clarify the requirements or decide the best solution if there are multiple approaches.

My plan is to create a new meta field on posts and custom posts for the primary category.
Primary category can be selected via dropdown from an array of categories that the post is assigned to. It means that any update to the post category assignment should be reflected on "primary category" meta too (assigning a post to another category should asynchronously update the "primary category" array). I am trying to predict edge case scenarios: 
If there is only one category that post is assigned to, that category will automatically be selected as the primary.If a primary category is selected and post assignment to that category is removed later, the primary category should be deselected.What happens when no category is selected.Next step is to create a new template that will display posts and custom posts that are assigned to a specific primary category. The template can be the same as a generic category template, except I will list posts that have a specific meta value - primary category.

0.5h - Creating plugin starter files

In order to create a Wordpress plugin, I have to create a new folder in ".../wp-content/plugins", and then create a PHP file.Since this is a very lightweight plugin with simple logic, I have decided to keep all logic in primary-category.php. For larger projects, I tend to split the files and make sure to obey the single-responsibility principle.

1h - Adding a meta field for the primary category 

I added a function with "add_meta_box" and added that function to the action hook "add_meta_boxes".Then I created a callback function that will render the primary category dropdown.When adding new meta values, we need to make sure that they are saved when the "save" button is clicked on the post, so I added a function with "update_post_meta". I added the "sanitize_text_field" method to enhance the security while saving text inputs to the database.

3h - Adding "primary category" support for custom categories

In the previous step, when I used "add_meta_box", I needed to define an array of post types where "primary category" meta will be added. It should contain 'posts', but also a dynamic array of all custom posts defined (or an empty array if there are no custom posts).
"get_post_types" is just what I needed, because it lists all post types and can exclude default post types with this argument: '_builtin' => false
By testing with my Portfolio plugin on a portfolio custom post type, I noticed that the "primary category" dropdown is shown, but with empty values. It happens because the taxonomy name is not 'category', but 'portfolio_category' instead. I created a logic that dynamically gets the taxonomy name if we are editing a custom post type (such as portfolio) and gets its categories by using 'get_the_terms'.
Now, I have all the selected categories in the primary category dropdown.

8h - Refreshing primary categories dropdown when a category is checked/unchecked

By default, the primary categories list is loaded via PHP, so checking/unchecking a category doesn't update the primary category list. 
In order to achieve this, I needed to create a new javascript file and include it in my main plugin php file. JQuery could also be used, however I prefer to use vanilla Javascript whenever possible. Next step is to create a function that will add/remove dropdown items when a category checkboxes are clicked.
In order to add/remove categories to primary categories dropdown, I need the names of checked categories and their ID. Category ID is not available, so I had to create a different approach: I loaded all categories of the current post type using REST API. I stored values in "categories" variable, and used it later to assign the correct category ID as a value for <option> in the primary category dropdown.
Javascript logic is the same as in the backend file: for regular posts, categories are loaded using "categories" endpoint, while custom post types are loaded using "terms".

1h - Creating a page template for primary categories

In my admin panel, I created "Primary category page", however I didn't see the template available in the page templates dropdown yet. I had to use filters"theme_page_templates" and "template_include" to include it. It worked great and now I have published the page.
At this point, my primary category template only contains <?php echo "test"; ?>
When visiting the page I only saw the text "test", which means that the correct template is being loaded. 
My local URL for a category with name "Cool Category" looks like this: http://localhost/site/primary-category-page/?primary_category=Cool%20Category

Next step would be to edit the template and query posts from a primary category based on query var "primary_category_id"

6h - Querying posts from a primary category

First, I added header and footer, then I created logic to fetch the category data.I reused logic to add custom post types to WP_Query arguments. Using documentation, I created the loop that lists the posts.
My next step is to create HTML for posts. On a real project, this would be created using custom HTML/CSS/PHP. For this exercise, I have decided it's best to reuse the styling from the category template to be more time efficient. I have created a new CSS file and copied CSS styles that I have found on category page.

Querying posts worked great for category post type, however when I tested with custom post type (Portfolio), it didn't work. I needed to add another query variable that will describe the post type of a category. 
I am now able to reach the category, however, WP_Query doesn't have posts yet even though I have assigned portfolios.
The issue was in $args of WP_Query: post types should be merged array of 'post' and custom post types.
With URL like this, I was now able to see list of posts assigned to a primary category: 
http://localhost/site/primary-category-page/?primary_category=Websites&type=portfolio_category

2h - Testing

After I finished all the features, I have gone through the code to check if I can optimise it. Then I checked how primary categories look and work one more time.

1.5h - Writing timelog description

TOTAL TIME: 25h



