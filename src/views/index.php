<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>

<body>
    <h2 style="text-align: center;background-color: silver">
        <i>@__index</i>
    </h2>
    <p><mark><i>curl -X GET :</i></mark></p>
    <?php
    echo "<pre>";
    print_r($this->fromGet);
    echo "</pre>";
    ?>
    <script>
        document.body.style.backgroundColor = "lightgray";
        const getParams = <?= json_encode($this->fromGet) ?>;

        console.log(JSON.stringify(getParams, null, 2));
    </script>
</body>

</html>