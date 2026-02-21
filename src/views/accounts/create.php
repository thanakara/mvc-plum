<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounts</title>
    <link rel="stylesheet" href="/fg/accounts.css">
</head>

<body>
    <h2 style="text-align: center;background-color: lightgray">
        <i>@__acc/create</i>
    </h2>
    <p><mark><i>curl -X GET :</i></mark></p>
    <?
    echo "<pre>";
    print_r($this->fromGet);
    echo "</pre>";
    ?>
    <h2 id="header2">SIGN IN</h2>
    <div style="text-align: center;">
        <form action="/accounts" method="post">
            <label for="account_name"><b>account_name: </b></label>
            <input type="text" name="account_name" placeholder="IGN" required>
            <label for="region"><b>region: </b></label>
            <select name="region" id="region" required>
                <option value="">---Select---</option>
                <option value="EU">EU</option>
                <option value="EUW">EUW</option>
                <option value="NA">NA</option>
                <option value="KR">KR</option>
            </select>
            <br><br>
            <label for="email"><b>email: </b></label>
            <input type="text" name="email" placeholder="a@b.com" required>
            <br><br>
            <button type="submit">Submit</button>
        </form>
    </div>
    <script>
        const getParams = <?= json_encode($this->fromGet) ?>;

        console.log(JSON.stringify(getParams, null, 2));
    </script>
</body>


</html>