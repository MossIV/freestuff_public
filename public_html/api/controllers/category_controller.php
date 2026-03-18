<?php

class CategoryController extends _Controller {

    /**
     * Get all categories
     * Returns all categories ordered by sort_order
     */
    public function get_all() {
        $categories = Category::getAllCategories();
        
        $output = array();
        foreach ($categories as $category) {
            $output[] = array(
                'category_id' => $category->category_id,
                'category_name' => $category->category_name,
                'category_slug' => $category->category_slug,
                'sort_order' => $category->sort_order
            );
        }
        
        echo json_encode($output);
    }

    /**
     * Get categories for a specific listing
     * @param int $listing_id
     */
    public function get_for_listing($listing_id) {
        $listing_id = (int)$listing_id;
        
        $category_ids = Category::getCategoriesForListing($listing_id);
        echo json_encode($category_ids);
    }
}
