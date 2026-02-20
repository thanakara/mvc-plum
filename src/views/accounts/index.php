<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="device-width, initial-scale=1.0">
    <title>Accounts</title>
</head>

<body>
    <h2 style="text-align: center;background-color: lightgray">
        <i>@__acc/index</i>
    </h2>
    <!-- 1. __get(): returns keys of params property -->
    <?
    $method = $_SERVER["REQUEST_METHOD"];
    $kwargs = ($method == "GET") ? $this->fromGet : $this->fromPost;
    echo "<p><mark><i>curl -X $method: </i></mark></p>";
    echo "<pre>";
    print_r($kwargs);
    echo "</pre>";
    ?>
</body>

</html>