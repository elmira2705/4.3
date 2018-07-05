<!doctype html>
<html lang="ru">
<head>
    <title>Домашнее задание к лекции 4.3 «SELECT из нескольких таблиц»</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<?php
error_reporting(E_ALL);

include 'dbconfig.php';

$pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
if (!$pdo) {
    die('Could not connect');
}
if (isset($_COOKIE["logged_in"])) {
    $login = $_COOKIE["logged_in"];
    $addButton = 'Добавить'; 
    //Запрос для формирования сводной таблицы с заданиями, id и именем автора, id и именем ответственного
    $select = "SELECT t.id as task_id, t.description as description, u.id as author_id, u.login as author_name, au.id as assigned_user_id, au.login as assigned_user_name, t.is_done as is_done, t.date_added as date_added FROM task t INNER JOIN user u ON u.id=t.user_id INNER JOIN user au ON t.assigned_user_id=au.id";
    //Удаление, выполнение задач, отправка данных для редактирования
    if($_GET) {
        $id = $_GET['id'];
        if ($_GET['action'] === 'delete') {
            $delPrep = $pdo->prepare("DELETE FROM task WHERE id = ?");
            $delPrep->execute([$id]);
            $description = $delPrep->fetch()['description'];
        }
        if ($_GET['action'] === 'done') {
            $donePrep = $pdo->prepare("UPDATE task SET is_done = TRUE WHERE id = ? LIMIT 1");
            $donePrep->execute([$id]);
            $description = $donePrep->fetch()['description'];
        }
        if ($_GET['action'] === 'edit') {
            $idPrep = $pdo->prepare("SELECT * FROM task WHERE id = ?");
            $idPrep->execute([$id]);
            $description = $idPrep->fetch()['description'];
            $addButton = 'Сохранить';
        }
    }
    //Добавление и редактирование задач
    if (isset($_POST['add'])) {
        $desc = $_POST['description'];
        $id = $_POST['id'];
        if ($id) {
            $editPrep = $pdo->prepare("UPDATE task SET description = ? WHERE id = ? LIMIT 1");
            $editPrep->execute([$desc, $id]);
        } else {
            $currentUser = $pdo->prepare("SELECT id, login FROM user WHERE login = ?");
            $currentUser->execute([$login]);
            $user = $currentUser->fetch();
            $addPrep = $pdo->prepare("INSERT INTO task (description, is_done, date_added, user_id, assigned_user_id) VALUES (?, ?, CURRENT_TIMESTAMP, ?, ?)");
            $addPrep->execute([$desc, false, $user['id'], $user['id']]);
        }
    }
    //Упорядочение задач
    $allowedSort = ['description', 'date_added', 'is_done'];
    if (isset($_POST['sort'])) {
        if(array_search($_POST['sortBy'], $allowedSort) !== false) {
            $sortBy = addslashes($_POST['sortBy']);
            $select .= " ORDER BY $sortBy";
        }
    }
    //Список пользователей для перекладывания ответственности
    $users = [];
    foreach ($pdo->query("SELECT `login` FROM user") as $user)
    {
        $users[] = $user['login'];
    }
    //Перекладывание ответственности
    if (isset($_POST['assign'])) {
        if (array_search($_POST['assign_to'], $users) !== false) {
            $assign_to_name = $pdo->quote($_POST['assign_to']);
            $taskId = $_POST['id'];
            $assign_to_id = $pdo->query("SELECT id, login FROM user WHERE login = $assign_to_name")->fetch()['id'];
            $assignPrep = $pdo->prepare("UPDATE task SET assigned_user_id = ? WHERE id = ? LIMIT 1");
            $assignPrep->execute([$assign_to_id, $taskId]);
        }
    }
?>
    <h1>Привет, <?=$login?>! Вот ваш список задач:</h1>
    <form method="post" action="./">
        <input type="hidden" name="id" value="<?= $_GET ? $_GET['id'] : "" ?>">
        <input placeholder="Описание задачи" name="description" value="<?= $_GET ? $description : "" ?>">
        <input type="submit" value="<?= $addButton ?>" name="add">
        Сортировать по:
        <select name="sortBy">
            <option value="description">Описанию</option>
            <option value="date_added">Дате добавления</option>
            <option value="is_done">Статусу</option>
        </select>
        <input type="submit" value="Отсортировать" name="sort">
    </form>
    <table>
        <tr>
            <th>Описание задачи</th>
            <th>Дата добавления</th>
            <th>Статус</th>
            <th>Действия</th>
            <th>Ответственный</th>
            <th>Автор</th>
            <th>Закрепить задачу за пользователем</th>
        </tr>
    <?php
        //Наполнение таблицы деталями каждой задачи
        $stmt = $pdo->prepare($select);
        $stmt->execute();
        $list = $stmt->fetchAll();
        foreach ($list as $row) {
            if ($login === $row['author_name']) { //Только задачи за авторством текущего пользователя
                echo '<tr>
                          <td>' . $row['description'] . '</td>
                          <td>' . $row['date_added'] . '</td>
                          <td>';
                            if (intval($row['is_done']) === 1)
                            {
                                echo '<span style="color: darkgreen">Выполнено</span>';
                            } elseif (intval($row['is_done']) === 0)
                            {
                                echo '<span style="color: darkorange">В процессе</span>';
                            } else
                                echo '<span style="color: red">В неопределенном состоянии</span>
                          </td>';
                          echo '<td><a href="index.php?id=' . $row['task_id'] . '&action=edit">Редактировать</a> ';
                            if ($login === $row['assigned_user_name'])
                            {
                                echo '<a href="index.php?id=' . $row['task_id'] . '&action=done"> Выполнить</a> ';
                            }
                            echo '<a href="index.php?id=' . $row['task_id'] . '&action=delete">Удалить</a>
                          </td>
                          <td>' . $row['assigned_user_name'] . '</td>
                          <td>' . $row['author_name'] . '</td>
                          <td>';
         ?>
                <form method="post" action="./">
                    <input type="hidden" name="id" value="<?= $row['task_id'] ?>">
                    <select name="assign_to">
                        <?php
                        foreach ($users as $user) {
                            if ($login !== $user) {
                                echo '<option>' . $user . '</option>';
                            }
                        }
                        ?>
                    </select>
                    <input type="submit" value="Переложить ответственность" name="assign">
                </form>
                </td></tr>

                <?php
            }
        }
        ?>
    </table>
    <h2>А вот что требуют от вас другие:</h2>
    <table>
    <tr>
        <th>Описание задачи</th>
        <th>Дата добавления</th>
        <th>Статус</th>
        <th>Действия</th>
        <th>Ответственный</th>
        <th>Автор</th>
    </tr>
    <?php
    foreach ($list as $row) {
        // Только задачи для текущего пользователя от других пользователей
        if ($login === $row['assigned_user_name'] && $login !== $row['author_name']) {
            echo '<tr>
                    <td>' . $row['description'] . '</td>
                    <td>' . $row['date_added'] . '</td>
                    <td>';
            if (intval($row['is_done']) === 1) {
                echo '<span style="color: darkgreen">Выполнено</span>';
            } elseif (intval ($row['is_done']) === 0) {
                echo '<span style="color: darkorange">В процессе</span>';
            } else
                echo '<span style="color: red">В неопределенном состоянии</span>';
            echo '</td>
                  <td><a href="index.php?id=' . $row['task_id'] . '&action=edit">Редактировать</a>
                      <a href="index.php?id=' . $row['task_id'] . '&action=done"> Выполнить</a>
                      <a href="index.php?id=' . $row['task_id'] . '&action=delete">Удалить</a>
                  </td>
                  <td>' . $row['assigned_user_name'] . '</td>
                  <td>' . $row['author_name'] . '</td>';
        }
    }

    ?>

    </table>
<br>
<a href="logout.php">Выйти</a>
<?php
} else {
    echo '<a href="register.php">Войдите или зарегистрируйтесь</a>';
}

?>
</body>
</html>
