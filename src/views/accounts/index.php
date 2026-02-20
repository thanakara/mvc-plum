<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounts</title>
</head>

<body>
    <h2 style="text-align: center;background-color: silver">
        <i>@__acc/index</i>
    </h2>
    <!-- 1. __get(): returns keys of params property -->
    <?
    $method = $_SERVER["REQUEST_METHOD"];
    $params = ($method == "GET") ? $this->fromGet : $this->fromPost;
    echo "<p><mark><i>curl -X $method: </i></mark></p>";
    echo "<pre>";
    print_r($params);
    echo "</pre>";
    ?>
    <script>
        document.body.style.backgroundColor = "lightgray";
        const params = <?= json_encode($params) ?>;

        console.log("Get/Post Attributes")
        console.log(params)
    </script>
</body>

</html>