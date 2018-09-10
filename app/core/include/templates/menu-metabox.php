<div id="separator-div">
    <div id="tabs-panel-separator-all" class="tabs-panel tabs-panel-active">
        <ul id="separator-checklist-pop" class="categorychecklist form-no-clear" >
            <?php echo walk_nav_menu_tree(array_map('wp_setup_nav_menu_item', [$separator]), 0, (object) ['walker' => $walker]); ?>
        </ul>

        <p class="button-controls">
            <span class="add-to-menu">
                <input type="submit"<?php wp_nav_menu_disabled_check($nav_menu_selected_id); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu' ); ?>" name="add-separator-menu-item" id="submit-separator-div" />
                <span class="spinner"></span>
            </span>
        </p>
    </div>
</div>
