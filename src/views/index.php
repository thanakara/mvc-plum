<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>

<body>
    <h2 style="text-align: center;background-color: lightgray">
        <i>_index@home_controller</i>
    </h2>
    <p><mark>$_GET:</mark></p>
    <!-- 1. __get(): returns keys of params property -->
    <?
    echo "<pre>";
    print_r($this->fromGet);
    echo "</pre>";
    ?>
    <!-- 2. extract() function inside View class [not recommended] -->
    <!-- var_dump($fromGet) -->
</body>

</html>