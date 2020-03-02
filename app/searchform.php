<?php
/**
 * Display search results
 *
 * Uses google custom search engine results
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.12
 */
?>

<div class="search">
    <div class="search__layer">

        <div class="search__field">
            <?php
                printf(
                    '<input class="search__field-input" id="search-input" type="text" placeholder="%s" autocomplete="off" spellcheck="false">',
                    __('Поиск&hellip;', 'knife-theme')
                );
            ?>
        </div>

        <?php
            printf(
                '<a class="search__button" target="_blank" href="/search/" id="search-button">%s</a>',
                __('Открыть полные результаты', 'knife-theme')
            );
        ?>

        <div class="search__results" id="search-results"></div>

    </div>
</div>

