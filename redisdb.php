<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .score-board {
            position: fixed;
            top: 30px;
            background-color: black;
            right: 30px;
            width: 204px;
            margin: 0 auto;
            font-size: 14px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 0 20px;
            border-radius: 5px;
            margin-top: 20px;
            height: 128px;
            text-align: center;
        }

        .cache-info {
            color: white;
        }

        .time-info {
            text-align: left;
            margin-top: 10px;
            color: white;
        }

        table {
            width: 100%;
            margin-top: 20px;
        }

        th,
        td {
            padding: 10px;
            text-align: center;
        }

        thead {
            background-color: #007bff;
            color: white;
        }

        tbody tr:nth-child(odd) {
            background-color: #f5f5f5;
        }

        tbody tr:hover {
            background-color: #cce5ff;
        }
    </style>
</head>

<body>

    <div class="container">
        <h3>Database and Cache Redis Time Result</h3>
        <?php
        require './vendor/autoload.php';

        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        $cachedUsers = $redis->get('user_info');
        $t0 = 0;
        $t1 = 0;

        if ($cachedUsers) {
            $cacheInfo = "<span style='color:skyblue'> Redis server </span>";
            $t0 = microtime(true) * 1000;
            $entries = explode('<br>', $cachedUsers);

            if (!empty($entries)) {
                echo '<table>';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Firstname</th>';
                echo '<th>Lastname</th>';
                echo '<th>Email</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';

                foreach ($entries as $entry) {
                    $userData = explode(' ', $entry);
                    echo '<tr>';
                    foreach ($userData as $data) {
                        echo '<td>' . $data . '</td>';
                    }
                    echo '</tr>';
                }

                echo '</tbody>';
                echo '</table>';
            }

            echo '</div>';
            $t1 = microtime(true) * 1000;
            $seconds = round($t1 - $t0, 3);

        } else {
            $cacheInfo = "<span style='color:lightgreen'> Database </span>";
            $t0 = microtime(true) * 1000;
            $conn = new mysqli('localhost:3306', 'root', '', 'redisphp');
            $sql = "select firstname, lastname, email from users;";
            $result = $conn->query($sql);
            $cachedtmp = '';

            echo '<table>';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Firstname</th>';
            echo '<th>Lastname</th>';
            echo '<th>Email</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $row['firstname'] . '</td>';
                echo '<td>' . $row['lastname'] . '</td>';
                echo '<td>' . $row['email'] . '</td>';
                echo '</tr>';
                $cachedtmp .= $row['firstname'] . ' ' . $row['lastname'] . ' ' . $row['email'] . '<br>';
            }

            echo '</tbody>';
            echo '</table>';
            $t1 = microtime(true) * 1000;
            $seconds = round($t1 - $t0, 3);

            $redis->set('user_info', $cachedtmp);
            $redis->expire('user_info', 10);
        }

        ?>
        <div class="score-board">
            <h3 class="cache-info">Score Board</h3>
            <p style="color:white; text-align:left;"> Fetch Data From : <?php echo $cacheInfo ?> </span></p>
            <p class="time-info"> Time Taken : <span style="color:white"> <?php echo $seconds ?> miliseconds</span></p>
        </div>
    </div>

</body>

</html>
