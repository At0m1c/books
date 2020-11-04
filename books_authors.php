<?php
$servername = 'localhost';
$username = '';
$password = '';
$database = '';

try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_GET['create'] == '1') {
        $conn->exec('CREATE TABLE IF NOT EXISTS authors(
          author_id integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
          name VARCHAR (255) NOT NULL
        )');
        $conn->exec('CREATE TABLE IF NOT EXISTS books(
          book_id integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
          title VARCHAR (255) NOT NULL,
          description TINYTEXT,
          author_id INTEGER,
          status INTEGER,
          date TIMESTAMP
        )');

        // индекс для поле статус
        $conn->exec('CREATE INDEX status ON books (status)');

        for ($i = 1; $i <= 100; $i++) {
            $data = [
                'name' => 'Автор ' .$i,
            ];
            $sql = "INSERT INTO authors (name) VALUES (:name)";
            $conn->prepare($sql)->execute($data);
        }

        for ($i = 1; $i <= 100; $i++) {
            $data = [
                'title' => 'Книга ' .$i,
                'description' => 'Описание книги ' .$i,
                'author_id' => rand(1, 100),
                'status' => rand(1, 5),
                'date' => date("Y-m-d H:i:s"),
            ];
            $sql = "INSERT INTO books (title, description, author_id, status, date) VALUES (:title, :description, :author_id, :status, :date)";
            $conn->prepare($sql)->execute($data);
        }
    }

    if ($_GET['page']) {
        $page = $_GET['page'];
    } else {
        $page = 1;
    }
    $limit = 3;
    $number = ($limit * $page) - $limit;

    $query = $conn->query("SELECT books.title, books.author_id, books.status, books.date, authors.author_id, authors.name 
        FROM books 
        JOIN authors 
        ON authors.author_id = books.author_id 
        WHERE status = 2
        ORDER BY books.date DESC LIMIT $number, $limit");
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $arItems[] = $row;
    }

    $numRows = $conn->query('select count(*) from books WHERE status = 2')->fetchColumn();
    $strPage = ceil($numRows / $limit);

    // или 2 вариант отдельно книги и авторы, работает быстрее
    $number = ($limit * $page) - $limit;
    $query1 = $conn->query("SELECT title, author_id, status, date 
        FROM books 
        WHERE status = 2 
        ORDER BY date 
        DESC LIMIT $number, $limit");
    while ($row1 = $query1->fetch(PDO::FETCH_ASSOC)) {
        $authorsIds[] = $row1['author_id'];
        $arItems1[] = $row1;
    }

    if (!empty($authorsIds)) {
        $uniqueAuthors = array_unique($authorsIds);
        $ids = implode(',', $uniqueAuthors);
        $queryAuthors = $conn->query("SELECT author_id, name FROM authors WHERE author_id IN ($ids)");
        while ($resAuthors = $queryAuthors->fetch(PDO::FETCH_ASSOC)) {
            $arAuthors[$resAuthors['author_id']] = $resAuthors['name'];
        }
    }

    foreach ($arItems1 as $key => $arItem) {
        $arBooks[$key] = $arItem;
        $arBooks[$key]['author_name'] = $arAuthors[$arItem['author_id']];
    }

} catch (PDOException $e) {
    echo "Connection failed: ".$e->getMessage();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Список книг</title>
</head>
<body>
<h1>Список книг</h1>
<?if (!empty($arItems)):?>
    <ul>
        <?foreach ($arItems as $arItem):?>
            <li><?=$arItem['title']?> - <?=$arItem['name']?></li>
        <?endforeach;?>
    </ul>
<?endif;?>
<?if (!empty($arBooks)):?>
    <ul>
        <?foreach ($arBooks as $arItem):?>
            <li><?=$arItem['title']?> - <?=$arItem['author_name']?></li>
        <?endforeach;?>
    </ul>
<?endif;?>
<?
for ($i = 1; $i <= $strPage; $i++) {
    echo "<a href='?page=$i'> $i </a>";
}
?>

</body>
</html>