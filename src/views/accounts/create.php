<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounts</title>
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

            <!-- <script> -->
        </form>
    </div>
    <script>
        const header2 = document.getElementById("header2");
        const getParams = <?= json_encode($this->fromGet) ?>;

        document.body.style.backgroundColor = "lightgray";

        header2.style.backgroundColor = "orangered";
        header2.style.textAlign = "center";
        header2.style.padding = "10px";
        header2.style.border = "2px solid gray";
        header2.style.borderRadius = "8px";
        header2.style.margin = "10px auto";
        header2.style.width = "30%";
        header2.style.fontSize = "24px";
        header2.style.fontWeight = "bold";

        console.log(getParams)
    </script>
</body>

</html>