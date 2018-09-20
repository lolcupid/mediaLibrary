<?php

function get_catalog_count($category = null) {
    include 'connection.php';
    $category = strtolower($category);
    try {
        $sql = "SELECT COUNT(media_id) FROM Media";

        if(!empty($category)) {
            $results = $db->prepare($sql . " WHERE LOWER(category) = :category");
            $results->bindParam(':category', $category, PDO::PARAM_STR);
        } else {
            $results = $db->prepare($sql);
        }
        $results->execute();
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    $count = $results->fetchColumn(0);
    return $count;
}

function full_catalog_array($limit = null, $offset = 0) {
    include 'connection.php';
    try {
        $sql = "SELECT media_id, title, category, img 
                FROM Media
                ORDER BY 
                REPLACE(
                    REPLACE(
                        REPLACE(title, 'The ', ''),
                        'An ', ''
                    ),
                    'A ', ''
                )";

        if(is_int($limit)) {
            $results = $db->prepare($sql . " LIMIT :limit OFFSET :offset");
            $results->bindParam(':limit', $limit, PDO::PARAM_INT);
            $results->bindParam(':offset', $offset, PDO::PARAM_INT);
        } else {
            $results = $db->prepare($sql);
        }
        $results->execute();
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    $catalog = $results->fetchAll(PDO::FETCH_ASSOC);
    return $catalog;
}

function category_catalog_array($category, $limit = null, $offset = 0) {
    include 'connection.php';
    $category = strtolower($category);
    try {
        $sql = "SELECT media_id, title, category, img 
                FROM Media
                WHERE LOWER(category) = :category
                ORDER BY 
                REPLACE(
                    REPLACE(
                        REPLACE(title, 'The ', ''),
                        'An ', ''
                    ),
                    'A ', ''
                )";

        if(is_int($limit)) {
            $results = $db->prepare($sql . " LIMIT :limit OFFSET :offset");
            $results->bindParam(':category', $category, PDO::PARAM_STR);
            $results->bindParam(':limit', $limit, PDO::PARAM_INT);
            $results->bindParam(':offset', $offset, PDO::PARAM_INT);
        } else {
            $results = $db->prepare();
            $results->bindParam(':category', $category, PDO::PARAM_STR);
        }
    $results->execute();
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    $catalog = $results->fetchAll(PDO::FETCH_ASSOC);
    return $catalog;
}

function random_catalog_array() {
    include 'connection.php';
    try {
        $results = $db->query(
            'SELECT media_id, title, category, img 
            FROM Media
            ORDER BY RAND()
            LIMIT 4'
            );
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    $catalog = $results->fetchAll(PDO::FETCH_ASSOC);
    return $catalog;
}

function single_item_array($id) {
    include 'connection.php';
    try {
        $results = $db->prepare(
            "SELECT Media.media_id, title, category, img, format, year, 
            publisher, isbn, genre 
            FROM Media
            JOIN Genres 
            ON Media.genre_id = Genres.genre_id
            LEFT OUTER JOIN Books 
            ON Media.media_id = Books.media_id
            WHERE Media.media_id = :id"
            );
        $results->bindValue(":id", $id, PDO::PARAM_INT);
        $results->execute();
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    $item = $results->fetch();
    if(empty($item)) return $item;

    try {
        $results = $db->prepare(
            "SELECT fullname, role 
            FROM Media_People
            JOIN People 
            ON Media_People.people_id = People.people_id
            WHERE Media_People.media_id = :id"
            );

        $results->bindValue(":id", $id, PDO::PARAM_INT);
        $results->execute();
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    
    while($row = $results->fetch(PDO::FETCH_ASSOC)) {
        $item[$row['role']][] = $row['fullname'];
    }

    return $item;
}

function get_item_html($item) {
    $output = "<li><a href='details.php?id="
        . $item["media_id"] . "'><img src='" 
        . $item["img"] . "' alt='" 
        . $item["title"] . "' />" 
        . "<p>View Details</p>"
        . "</a></li>";
    return $output;
}

// function array_category($catalog,$category) {
//     $output = array();
    
//     foreach ($catalog as $id => $item) {
//         if ($category == null OR strtolower($category) == strtolower($item["category"])) {
//             $sort = $item["title"];
//             $sort = ltrim($sort,"The ");
//             $sort = ltrim($sort,"A ");
//             $sort = ltrim($sort,"An ");
//             $output[$id] = $sort;            
//         }
//     }
    
//     asort($output);
//     return array_keys($output);
// }