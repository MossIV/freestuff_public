<?
class Category extends CRModel {
    public $category_id;
    public $category_name;
    public $category_slug;
    public $sort_order;

    /**
     * class constructor
     */
    public function __construct($category_id = false, $category_name = '', $category_slug = '', $sort_order = 0) {
        $this->category_id = $category_id;
        $this->category_name = $category_name;
        $this->category_slug = $category_slug;
        $this->sort_order = $sort_order;
        $this->_primary_key = 'category_id';
        $this->_table_name = 'category';
    }

    /**
     * retrieve values from $_POST and set it to the object
     */
    public function buildFromPost() {
        $this->_populateFromArray($_POST);
    }

    /**
     * retrieve database record and set it to the object
     * @param INT $category_id
     */
    public function retrieveFromID($category_id) {
        $category_id = (int)$category_id;

        $sql = "SELECT  category_id, category_name, category_slug, sort_order
                FROM category 
                WHERE category_id = " . quoteSQL($category_id);
        $row = runQueryGetFirstRow($sql);
        if ($row) {
            $this->_populateFromArray($row);
        }
    }

    /**
     * Get all categories ordered by sort_order
     * @return array Array of Category objects
     */
    public static function getAllCategories() {
        $sql = "SELECT category_id, category_name, category_slug, sort_order
                FROM category 
                ORDER BY sort_order ASC, category_name ASC";
        $result = runQuery($sql);
        
        $categories = array();
        while ($row = fetchSQL($result)) {
            $category = new Category(
                $row['category_id'],
                $row['category_name'],
                $row['category_slug'],
                $row['sort_order']
            );
            $categories[] = $category;
        }
        return $categories;
    }

    /**
     * Get all categories as an array suitable for dropdowns
     * @return array Array with id => name pairs
     */
    public static function getCategoriesForDropdown() {
        $categories = self::getAllCategories();
        $output = array();
        foreach ($categories as $category) {
            $output[$category->category_id] = $category->category_name;
        }
        return $output;
    }

    /**
     * Get categories for a specific listing
     * @param int $listing_id
     * @return array Array of category IDs
     */
    public static function getCategoriesForListing($listing_id) {
        $listing_id = (int)$listing_id;
        
        $sql = "SELECT category_id 
                FROM listing_category 
                WHERE listing_id = " . quoteSQL($listing_id);
        return runQueryGetAllFirstValues($sql);
    }

    /**
     * Save category associations for a listing
     * @param int $listing_id
     * @param array $category_ids Array of category IDs
     */
    public static function saveCategoriesForListing($listing_id, $category_ids) {
        $listing_id = (int)$listing_id;
        
        // Delete existing associations
        $sql = "DELETE FROM listing_category WHERE listing_id = " . quoteSQL($listing_id);
        runQuery($sql);
        
        // Insert new associations
        if (!empty($category_ids) && is_array($category_ids)) {
            foreach ($category_ids as $category_id) {
                $category_id = (int)$category_id;
                if ($category_id > 0) {
                    $sql = "INSERT INTO listing_category (listing_id, category_id) 
                            VALUES (" . quoteSQL($listing_id) . ", " . quoteSQL($category_id) . ")";
                    runQuery($sql);
                }
            }
        }
    }

    /**
     * Validate value
     * @param STRING $type
     */
    public function validate($type = NULL) {
        if ($type != 'insert') {
            $this->_validateRequiredField('category_id', 'Category Id');
        }
        $this->_validateRequiredField('category_name', 'Category Name');
        $this->_validateRequiredField('category_slug', 'Category Slug');

        return !hasErrors();
    }

    /**
     * Insert record to database
     */
    public function insert() {
        if ($this->validate('insert')) {
            $sql = "INSERT category SET ";
            $sql .= $this->_sqlSETHelper('category_name', 'category_slug', 'sort_order');

            if (runQuery($sql)) {
                $this->category_id = lastInsertedId();
                return true;
            }
        }
    }

    /**
     * Update db record
     */
    public function update() {
        if ($this->validate()) {
            $sql = "UPDATE category SET ";
            $sql .= $this->_sqlSETHelper('category_name', 'category_slug', 'sort_order');
            $sql .= " WHERE category_id = " . quoteSQL($this->category_id);
            return runQuery($sql);
        }
    }

    /**
     * Delete record from db
     */
    public static function delete($category_id) {
        $category_id = (int)$category_id;

        $sql = "DELETE FROM category 
                WHERE category_id = " . quoteSQL($category_id);
        return runQuery($sql);
    }

    /**
     * Look up the name of a record, based on the id
     */
    public static function resolve($category_id) {
        $category_id = (int)$category_id;
        
        $sql = "SELECT category_id, category_name, category_slug, sort_order
                FROM category 
                WHERE category_id = " . quoteSQL($category_id);
        $row = runQueryGetFirstRow($sql);
        
        if ($row) {
            return new Category(
                $row['category_id'],
                $row['category_name'],
                $row['category_slug'],
                $row['sort_order']
            );
        }
        return false;
    }

    /**
     * Display category name by ID
     */
    public static function display($category_id) {
        $category = self::resolve($category_id);
        if ($category) {
            return $category->category_name;
        }
        return '';
    }
}
