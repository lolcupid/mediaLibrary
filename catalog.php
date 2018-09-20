<?php 
include("inc/functions.php");
$catalog = full_catalog_array();

$pageTitle = "Full Catalog";
$section = null;
$total_item_page = 8;

if (isset($_GET["cat"])) {
    if ($_GET["cat"] == "books") {
        $pageTitle = "Books";
        $section = "books";
    } else if ($_GET["cat"] == "movies") {
        $pageTitle = "Movies";
        $section = "movies";
    } else if ($_GET["cat"] == "music") {
        $pageTitle = "Music";
        $section = "music";
    }
}

if(!empty($_GET['pg'])) {
    $current_page = filter_input(INPUT_GET, 'pg', FILTER_SANITIZE_NUMBER_INT);
}

if(empty($current_page)) {
    $current_page = 1;
}

$total_items = get_catalog_count($section);
$total_pages = ceil($total_items / $total_item_page);
$offset = ($current_page - 1) * $total_item_page;
$limit_results = '';

if(!empty($section)) {
    $limit_results = 'cat=' . $section . '&';
}

if($current_page > $total_item_page) {
    header('location: catalog.php?' . $limit_results . 'pg=' . $total_pages);
}

if($current_page < 1) {
    header('location: catalog.php?' . $limit_results . 'pg=1');
}

if(empty($section)) {
    $catalog = full_catalog_array($total_item_page, $offset);
} else {
    $catalog = category_catalog_array($section, $total_item_page, $offset);
}

$pagination = "<div class=\"pagination\">";
$pagination .= "Pages: ";
for($i=1; $i <= $total_pages; $i++) {
    if($i == $current_page) {
        $pagination .= " <span>{$i}</span>";
    } else {
        $pagination .= " <a href='catalog.php?";
        if(!empty($section)) {
            $pagination .= "cat={$section}&";
        }
        $pagination .= "pg={$i}'>{$i}</a>";
    }
}
$pagination .= "</div>";

include("inc/header.php"); ?>

<div class="section catalog page">
    
    <div class="wrapper">
        
        <h1>
        <?php 
            if ($section != null) {
                echo "<a href='catalog.php'>Full Catalog</a> &gt; ";
            }
            echo $pageTitle; 
        ?>
        </h1>
        <?php echo $pagination; ?>
        <ul class="items">
            <?php
            foreach ($catalog as $item) {
                echo get_item_html($item);
            }
            ?>
        </ul>
        <?php echo $pagination; ?>
    </div>
</div>

<?php include("inc/footer.php"); ?>