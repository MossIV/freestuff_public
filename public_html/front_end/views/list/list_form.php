<div class="container">
    <?
    define("IS_UPDATE_MODE", !empty($listing->listing_id));

    $page_heading = IS_UPDATE_MODE ? 'Edit my listing' : 'Create a listing';
    TemplateHandler::echoPageTitle($page_heading);

    // Default button style for rotate button
    $btn_style = 'btn-secondary';

    // Prepare districts data for JavaScript
    $districts_json = json_encode($districts);
    ?>
    <form method="post" action="list/save" id='list_form' class="row">
        <? /* [ML] populated by croppie*/ ?>
        <input type="hidden" name="image_data">

        <div class="col-12 px-0 mb-3 mb-md-0">
            <input type='hidden' name='temp_id' value="<?= ($listing->temp_id) ?>"/>
            <input type="hidden" name="listing_id" value="<?= ($listing->listing_id) ?>"/>

            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 col-md-6 mb-3 mb-md-0">
                        <div class="row p-3" id="listing_type">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input  mr-2" type="radio" name="listing_type" id="radio-1-1"
                                       value="free" <?= ($listing->listing_type == 'free' ? 'checked' : '') ?> />
                                <label class="form-check-label" for="radio-1-1">Stuff I'm Giving Away</label>
                            </div>
                            <div class="form-check form-check-inline ml-3">
                                <input class="form-check-input  mr-2" type="radio" name="listing_type" id="radio-1-2"
                                       value="wanted"<?= ($listing->listing_type == 'wanted' ? 'checked' : '') ?> />
                                <label class="form-check-label" for="radio-1-2">Stuff I Want</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" id="title" class="form-control" name="title"
                                   value="<?= ($listing->title) ?>"/>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="text_limit_box form-control" id="description" name="description"
                                      maxlength="250"><?= ($listing->description) ?></textarea>
                            <small class="text_limit_countdown form-text text-muted">250 characters left</small>
                        </div>
                        <div class="form-group">
                            <label for="district_id_field">Closest District for pickup</label>
                            <div class="twitter-typeahead">
                                <input type="text" 
                                       class="form-control typeahead-district" 
                                       id="district_search" 
                                       placeholder="Type to search districts..."
                                       autocomplete="off"
                                       value="<?= (IS_UPDATE_MODE && !empty($listing->district_id) ? District::display($listing->district_id) : '') ?>"/>
                                <input type="hidden" name="district_id" id="district_id_field" value="<?= ($listing->district_id) ?>"/>
                            </div>
                        </div>

                        <?php
                        // Category Tags Section
                        if (!empty($categories)) {
                            ?>
                            <div class="form-group">
                                <label for="category_ids">Category Tags</label>
                                <div class="category-tags-container">
                                    <?php
                                    foreach ($categories as $category_id => $category_name) {
                                        $is_checked = !empty($listing_category_ids) && in_array($category_id, $listing_category_ids);
                                        ?>
                                        <div class="form-check form-check-inline category-tag-checkbox">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="category_ids[]" 
                                                   id="category_<?= $category_id ?>" 
                                                   value="<?= $category_id ?>"
                                                   <?= ($is_checked ? 'checked' : '') ?> />
                                            <label class="form-check-label" for="category_<?= $category_id ?>">
                                                <?= htmlspecialchars($category_name) ?>
                                            </label>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <small class="form-text text-muted">Select one or more categories for your listing</small>
                            </div>
                            <?php
                        }
                        ?>

                    </div>

                    <div class="col-12 col-md-6">
                        <div id='uploadedImage'>
                            <!-- Upload Box -->
                            <div class="upload-box mb-3" id="uploadBox">
                                <div class="upload-box-content">
                                    <i class="fa fa-camera-retro upload-icon"></i>
                                    <p class="upload-text">Click to upload a picture</p>
                                    <p class="upload-hint">or drag and drop</p>
                                    <label class='btn primary upload-btn'>
                                        Upload Picture
                                        <input class="d-none" type="file" id="upload" value="Upload Picture"
                                               name="pickfiles" accept="image/*"/>
                                    </label>
                                </div>
                            </div>
                            <div id="rotate" class="btn <?= ($btn_style) ?> mb-2"><span class="fa fa-refresh"></span>
                                Rotate
                            </div>
                            <div id="picture" style="display: none;"></div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 mb-3">
            <?
            if (!IS_UPDATE_MODE) {
                ?>
                <div class="form-check">
                    <input type="checkbox" name="pickup" id="cb-1-1" class="form-check-input"/>
                    <label for="cb-1-1" class="form-check-label ml-2">
                        <span class="agree" id="agree-free">I understand all items listed on Freestuff must be available free for pickup with no strings attached</span>
                        <span class="agree d-none" id="agree-wanted">I agree to all <a href="page/terms"
                                                                                       target="_blank">Terms &amp; Conditions</a></span>
                    </label>
                </div>
                <?
            }
            ?>
        </div>

        <div class="col-12">
            <?
            if (IS_UPDATE_MODE) {
                ?>
                <button type="button" class="editing btn primary btn-submit-form">Update My Listing</button>
                <button class="btn btn-danger ajax-modal"
                        data-href="<?= (APP_URL) ?>list/delete_modal/<?= $listing->listing_id ?>">
                    Delete My Item
                </button>
                <button type="button"
                        data-return="<?= (seoFriendlyURLs($listing->listing_id, "listing", false, $listing->title)) ?>"
                        class='btn btn-light btn-cancel-edit'>Cancel
                </button>
                <?
            } else {
                ?>
                <div class="form-row">
                    <button type="button" class="btn primary btn-submit-form btn-mobile">Place My Listing</button>
                </div>
                <?
            }
            ?>
        </div>
    </form>

    <script type="text/javascript">
        var districtsData = <?= $districts_json ?>;
    </script>
</div>
