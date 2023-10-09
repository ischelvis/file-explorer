<?php
$maindir = getcwd();
$tokens = explode('\\', $maindir);
$maindir = trim(end($tokens));

if (isset($_GET['selected_dir'])) {
    if (!str_contains(realpath($_GET['selected_dir']), $maindir)) {
        echo "Het is niet toegestaan om de huidige werkmap te verlaten.";
        echo '<br>';
        echo '<a href="index.php">Ga terug</a>';
        exit();
    }
}
?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Filebrowser</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="navbar-container">
    <div class="navbar">
        <?php
        $homepath = getcwd();
        echo '<ul class="crumblist">';
        echo '<li class="crumb"><a href="index.php?selected_dir=' . $homepath . '" >Home</a></li>';

        if (isset($_GET['selected_dir'])) {
            $crumbpath = $homepath;
            $crumbcut = 3;
            $breadcrumbs = explode('\\', realpath($_GET['selected_dir']));

            $crumbcounter = 0;
            foreach ($breadcrumbs as $crumb){
                if ($crumbcounter <= $crumbcut) {
                } else {
                    echo '<li class="crumb"><a href="index.php?selected_dir=' . $crumbpath . "\\" . $crumb . '" >' . ">&nbsp&nbsp" . $crumb . '</a></li>';
                    $crumbpath = $crumbpath . "\\" . $crumb;
                }
                $crumbcounter++;
            }

        }
        echo '</ul>';
        ?>
    </div>
</div>

<div class="browser">
    <h1>Mappen/Bestanden</h1>
    <?php
    $dir = getcwd();
    if (isset($_GET['selected_dir'])) {
        $dir = $_GET['selected_dir'];
    }
    $scanned_dir = scandir($dir);

    $tokens2 = explode('\\', realpath($dir));
    $dircheck = trim(end($tokens2));
    if (str_contains($dircheck, $maindir)){
        $scanned_dir = array_splice($scanned_dir, 2);
    } else {
        $scanned_dir = array_splice($scanned_dir, 1);
    }

    echo '<ul class="browser-list">';
    $x = 0;
    foreach ($scanned_dir as $value)
    {
        if (is_dir($dir . "/" . $value))
        {
            echo '<li  class="foldericon browser-list-item"><a href="index.php?selected_dir=' . $dir . "\\" . $value . '">' . $value . '</a></li>';
        }
        else
        {
            echo '<li  class="fileicon browser-list-item"><a href="index.php?selected_dir=' . $dir . "&selected_file=" . $value . '">' . $value . '</a></li>';
        }
        $x++;
    }
    echo '</ul>';
    ?>
</div>

<div class="fileviewer">
    <?php
    if (isset($_GET['selected_file'])) {
        echo '<h1>Inhoud</h1>';
        echo '<p>Bestand: ' . $_GET['selected_file'] . '</p>';
        echo '<p>Grote: ' . filesize($dir . "\\" . $_GET['selected_file']) / 1000000 . "mb" . '</p>';
        if (is_writable($dir . "\\" . $_GET['selected_file'])){
            echo '<p>Schrijfbaar: Ja</p>';
        } else {
            echo '<p>Schrijfbaar: Nee</p>';
        }
        echo  '<p>Laatst aangepast: ' . date("d F Y - H:i:s.", filemtime($dir . "\\" . $_GET['selected_file'])) . '</p>';

        $mime = mime_content_type($dir . "\\" . $_GET['selected_file']);

        if ($mime == "text/plain") {
            echo '<form method="post">';
            echo '<textarea class="textviewer" name="text">' . file_get_contents($dir . "\\" . $_GET['selected_file']) . '</textarea>';
            echo '<br>';
            echo '<br>';
            echo '<input class="button1" type="submit" value="Verzenden">';
            echo '<br>';
            echo '</form>';
        }

        if (str_contains($mime, 'image')) {
            $imgpath = str_replace(getcwd(), '', $_GET['selected_dir']);
            $imgpath = ltrim($imgpath, '\\');
            $imgpath = $imgpath . "\\";
            echo '<img src=' . $imgpath . "\\" . $_GET['selected_file'] . '>';
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        file_put_contents($dir . "\\" . $_GET['selected_file'], $_POST['text']);
        header("Refresh:0");
    }
    ?>
</div>
</body>
</html>