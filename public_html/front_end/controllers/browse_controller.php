<?php
class BrowseController extends _Controller  {

    public function index($region_name) {
        $region_name = trim($region_name);

        if (!$region_name) {
            redirect("/");
        }

        $listing_filter = new FilterHelper('listings');
        $listing_filter->setDefault('listing_type','free');

        $ip = paramFromHash('REMOTE_ADDR', $_SERVER);

        $district_ids = District::districtIdsForRegion($region_name);
        if (!sizeof($district_ids)) {
            redirect("/");
        }

        $sql = "SELECT l.*,u.firstname 
                FROM listing l 
                JOIN user u on l.user_id = u.user_id 
                WHERE l.listing_status IN ('available','reserved') 
                AND l.district_id in " . quoteIN($district_ids);
        if ($listing_filter->listing_type != 'all') {
        //    $sql .= " AND listing_type =" . quoteSQL($listing_filter->listing_type);
        }

        $listings = new DataWindowHelper("browse", $sql, "listing_date", "desc", 20);
        $listings->run();
        $paging = $listings->getPaging();

        //profile mark category
        $sql = "INSERT category_profile_mark SET 
                category = " . quoteSQL($region_name) . ", 
                user_id = " . quoteSQL(SESSION_USER_ID) . ", 
                date = NOW(), 
                ip_address = " . quoteSQL($ip);
        runQuery($sql);

        PageHelper::setMetaTitle('Browse ' . $region_name);
        PageHelper::setMetaDescription('Browse ' . $region_name);
        PageHelper::setRssLink(APP_URL . 'rss_feed?category=' . $region_name);

        TemplateHandler::setBrowseCategoryName($region_name);
        PageHelper::setViews('views/search/banner.php', "views/search/search_results.php");

        BreadcrumbHelper::addBreadcrumbs("Browse Listings");
        BreadcrumbHelper::addBreadcrumbs($region_name);

        include("templates/main_layout.php");
    }

    public function byRegion($region) {
        $this->index($region);
    }

    /**
     * Browse listings by category
     * @param string $category_slug The category slug
     */
    public function byCategory($category_slug) {
        $category_slug = trim($category_slug);

        if (!$category_slug) {
            redirect("/");
        }

        // Find category by slug
        $category = Category::getBySlug($category_slug);
        if (!$category) {
            redirect("/");
        }

        $listing_filter = new FilterHelper('listings');
        $listing_filter->setDefault('listing_type','free');

        $ip = paramFromHash('REMOTE_ADDR', $_SERVER);

        // Join with listing_category table to filter by category
        $sql = "SELECT l.*,u.firstname 
                FROM listing l 
                JOIN user u on l.user_id = u.user_id 
                JOIN listing_category lc ON l.listing_id = lc.listing_id
                WHERE l.listing_status IN ('available','reserved') 
                AND lc.category_id = " . quoteSQL($category->category_id);
        if ($listing_filter->listing_type != 'all') {
            //    $sql .= " AND listing_type =" . quoteSQL($listing_filter->listing_type);
        }

        $listings = new DataWindowHelper("browse", $sql, "listing_date", "desc", 20);
        $listings->run();
        $paging = $listings->getPaging();

        //profile mark category
        $sql = "INSERT category_profile_mark SET 
                category = " . quoteSQL($category->category_name) . ", 
                user_id = " . quoteSQL(SESSION_USER_ID) . ", 
                date = NOW(), 
                ip_address = " . quoteSQL($ip);
        runQuery($sql);

        PageHelper::setMetaTitle('Browse ' . $category->category_name);
        PageHelper::setMetaDescription('Browse ' . $category->category_name);
        PageHelper::setRssLink(APP_URL . 'rss_feed?category=' . $category->category_name);

        TemplateHandler::setBrowseCategoryName($category->category_name);
        PageHelper::setViews('views/search/banner.php', "views/search/search_results.php");

        BreadcrumbHelper::addBreadcrumbs("Browse Listings");
        BreadcrumbHelper::addBreadcrumbs($category->category_name);

        include("templates/main_layout.php");
    }

    /**
     * Save category search notification
     * @param string $category The category name
     */
    public function saveCategory($category) {
        self::loginRequiredAndRedirect();

        SavedSearch::getRegionNotifications($category);

        redirect(APP_URL . 'search');
    }

    public function save($region) {
        self::loginRequiredAndRedirect();

        SavedSearch::getRegionNotifications($region);

        redirect(APP_URL . 'search');
    }

    public function toggleWanted() {
        $show_wanted = paramFromGet('show_wanted');

        if (empty($show_wanted)) {
            $_SESSION["listing_filter"] = array('free');
        } else {
            $_SESSION["listing_filter"] = explode(',', $show_wanted);
        }
        exit();
    }
}
