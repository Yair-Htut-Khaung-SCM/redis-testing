<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            background-color: #f5f5f5;
            margin: 0;
            padding: 60px;
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
            width: 221px;
            margin: 0 auto;
            font-size: 14px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 0 20px;
            border-radius: 5px;
            margin-top: 20px;
            height: 128px;
            text-align: center;
        }

        .time-info {
            text-align: left;
            margin-top: 10px;
            color: white;
        }

        .cache-info {
            color: white;
        }


    </style>
</head>

<body>

    <h3>RestAPI and Cache Redis Time Result</h3>
    

    <?php
    require './vendor/autoload.php';

    $photos = [];
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);

    $cachedPhotos = $redis->get('photos');
    $t0 = 0;
    $t1 = 0;

    if ($cachedPhotos) {
        $cacheInfo = "<span style='color:skyblue'> Redis server </span>";

        $t0 = microtime(true) * 1000;
        $photos = json_decode($cachedPhotos);
    } else {
        $cacheInfo = "<span style='color:lightgreen'> Rest Api </span>";

        $t0 = microtime(true) * 1000;
        $httpClient = new GuzzleHttp\Client(['base_uri' => 'https://jsonplaceholder.typicode.com/', 'verify' => false]);
        $response = $httpClient->request('GET', 'photos');
        $photos = json_decode($response->getBody());

        // Set cache in Redis
        $redis->set('photos', json_encode($photos));
        $redis->expire('photos', 10);
    }
    ?>

    <!-- Display photos -->
    <?php foreach ($photos as $photo) : ?>
        <div class="container">
            <p>Album id: <?php echo $photo->albumId; ?></p>
            <p>Title: <?php echo $photo->title; ?></p>
            <p>Url: <?php echo $photo->url; ?></p>
            <p>ThumbnailUrl: <?php echo $photo->thumbnailUrl; ?></p>
        </div>
    <?php endforeach;

    $t1 = microtime(true) * 1000;
    $seconds = round($t1 - $t0, 3);
    ?>

    <div class="score-board">
        <h3 class="cache-info">Score Board</h3>
        <p style="color:white; text-align:left;"> Fetch Data From : <?php echo $cacheInfo ?> </span></p>
        <p class="time-info"> Time Taken : <span style="color:white"> <?php echo $seconds ?> milliseconds</span></p>
    </div>

</body>

</html>